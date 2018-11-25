<?php
require_once('common.php');
class Ticket
{
	private $data;
	private $loc;
	function __construct($loc)
	{
		$this->loc = $loc;
		$json_string = file_get_contents($loc);
		$ticketdata = json_decode($json_string);
		$this->data = $ticketdata;
	}
	function __clone()
	{
		$this->data = clone $this->data;
	}
	public function __set($name,$value)
	{
		$ticket = $this->data;
		switch($name)
		{
			/*case 'owner':
				$ticket->owner = $value;
				break;
			case 'ownerstate':
				$ticket->ownerstate = $value;
				break;*/
			case 'ownerstatenumber':
				$ticket->ownerstatenumber = $value;
				break;
			case 'default':
				echo $name." set property not handled in ticke class".EOL;
				break;
		}		
	}
	public function __get($name)
	{
		$ticket = $this->data;
		switch($name)
		{
			case 'data':
				return $ticket;
				break;
			case 'type':
				return $ticket->type;
				break;
			case 'number':
				return $ticket->number;
				break;
			case 'nextaction':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					if(isset($state->nextaction))
						return $state->nextaction;
				}
				return null;
			case 'path':
				return $ticket->path;
			case 'revertable':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					if(isset($state->revert))
						return 1;
					return 0;
				}
				return 0;
			case 'days':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					if(isset($state->days))
						return $state->days;
					return null;
				}
				return null;
			case 'statename':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					return $state->name;
				}
				return null;
			case 'activatedby':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					return $state->activatedby;
				}
				return null;
			case 'activated':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					return $state->activated;
				}
				return null;
			case 'instancecount':
				if(isset($ticket->ownerstatenumber))
				{
					$statenumber = $ticket->ownerstatenumber;
					return $ticket->state->$statenumber;
				}
				return 0;
				break;
			case 'waitstatetime':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					if(isset($state->waitstate))
						return $state->waitstate;
					return false;
				}
				break;
			case 'ownerstate':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					return $state;
				}
				return null;
			case 'owner':
				if(isset($ticket->ownerstatenumber))
				{
					$state = $ticket->states[$ticket->ownerstatenumber];
					return $state->assignee;
				}
				return null;
			case 'id':
			case 'ownerstatenumber':
				if(isset($ticket->ownerstatenumber))
					return $ticket->ownerstatenumber;
				return -1;
			case 'title':
				return $ticket->title;
				break;
			case 'assignee':
				$assignee = array();
				$cstates = $this->cstates;
				foreach($this->cstates as $state)
				{
					$assignee[] = $state->assignee;
				}
				return $assignee;
				break;
			case 'cstates':
				$states = array();
				foreach($ticket->state as $number=>$count)
				{
					if(array_key_exists($number,$ticket->states))
					{
						$state = $ticket->states[$number];
						//for($i=0;$i<$count;$i++)
						//{
						$states[$number][] = $ticket->states[$number];
						//}
					}
					else
						echo "Invalid state".EOL;		
					
				}
				return $states;
				break;
			
			case 'default':
				echo $name." get property not handled in ticke class".EOL;
				break;
		}
		
	}
	public function PrevWaitState()
	{
		$ticket = $this->data;
		$ownerstatenumber = $this->ownerstatenumber;
		if(isset($ticket->state->$ownerstatenumber))
		{
			$cstate = $this->ownerstate;
			if(isset($cstate->waitstate))
			{
				unset($cstate->waitstate);
				$this->Save();
			}
		}
	}
	public function NextWaitState($action)
	{
		$ticket = $this->data;
		$ownerstatenumber = $this->ownerstatenumber;
		if(isset($ticket->state->$ownerstatenumber))
		{
			$state = $ticket->states[$ownerstatenumber];
			if(!isset($state->waitstate))
			{
				$state->waitstate = strtotime("now");
				if($action == ACTION_REVERT_WAIT)
					$state->nextaction = 'revert';
				else
					$state->nextaction = 'next';
			}
			//echo $state->waitstate.EOL;
			/*$state->ownerstatenumber = $ticket->ownerstatenumber;
			$state->ownerstate = $ticket->ownerstate;
			$state->owner = $ticket->owner;*/
		
			unset($this->ownerstatenumber);
			/*unset($this->ownerstate);
			unset($this->owner);*/
			
			$this->Save();
		}
		else
			echo "State error".EOL;
	}
	public function RevertState()
	{
		$ticket = $this->data;
		$ownerstatenumber = $this->ownerstatenumber;
		if(isset($ticket->state->$ownerstatenumber))
		{
			$cstate = $this->ownerstate;
			
			if(isset($cstate->waitstate))
				unset($cstate->waitstate);
			$ticket->state->$ownerstatenumber--;
			//if($ticket->state->$ownerstatenumber == 0)
			//{
			unset($ticket->state->$ownerstatenumber);
			$state = $ticket->states[$ownerstatenumber];
			$preowner = $state->assignee;
			$ns = $state->revert;
			if(isset($ticket->state->$ns))
				$ticket->state->$ns++;
			else
				$ticket->state->$ns = 1;
			$state = $ticket->states[$ns];
			if(!isset($state->activated))
			{
				if(isset($state->closed))
					unset($state->closed);
				$state->activatedby = $preowner;
				$state->activated = Date('Y-m-d');
			}
			else
				$state->activatedby .= "/".$preowner;				
			unset($ticket->ownerstatenumber);
			/*unset($ticket->ownerstate);
			unset($ticket->owner);*/
			$this->Save();
		}
		else
			echo "State error".EOL;
	}
	public function NextState()
	{
		$ticket = $this->data;
		$ownerstatenumber = $this->ownerstatenumber;
		if(isset($ticket->state->$ownerstatenumber))
		{
			$cstate = $this->ownerstate;  // identify the current state
			
			if(isset($cstate->waitstate))  // if this state is in wait state , remove it
				unset($cstate->waitstate);
			$ticket->state->$ownerstatenumber--;    // reduce the state owners count
			//if($ticket->state->$ownerstatenumber == 0)
			//{
			unset($ticket->state->$ownerstatenumber); 
			$state = $ticket->states[$ownerstatenumber];
			$state->closed = Date('Y-m-d');//  mark this state as closed
			$preowner = $state->assignee; // remember the owner
			//}
			
			if(isset($cstate->next)) // if next state is defined 
			{
				if($cstate->next == 'end') // do all to close the ticket
				{
					$ticket->closed = Date('Y-m-d');
				}
				else   
				{
					$nextstates = explode(",",$cstate->next);// find all next states 
					foreach($nextstates as $ns)    // activate next states 
					{
						if($ns < count($ticket->states)) // if valid states
						{
							if(isset($ticket->state->$ns))
								$ticket->state->$ns++;
							else
								$ticket->state->$ns = 1;
							$state = $ticket->states[$ns];
							if(!isset($state->activated))
							{
								if(isset($state->closed))
									unset($state->closed);
								$state->activatedby = $preowner;
								$state->activated = Date('Y-m-d');
							}
							else
								$state->activatedby .= "/".$preowner;
						}
					}
				}
			}
			else
			{
				$ns = $ticket->ownerstatenumber+1;
				if($ns < count($ticket->states))
				{
					if(isset($ticket->state->$ns))
						$ticket->state->$ns++;
					else
						$ticket->state->$ns = 1;
					$state = $ticket->states[$ns];
					if(!isset($state->activated))
					{
						if(isset($state->closed))
							unset($state->closed);
						$state->activatedby = $preowner;
						$state->activated = Date('Y-m-d');
					}
					else
						$state->activatedby .= "/".$preowner;				
				}
				else
				{
					// This ticket is closed if there are no empty states
					if(count((array)$ticket->state)==0)
						$ticket->closed = Date('Y-m-d');
				}
			}
			unset($ticket->ownerstatenumber);
			/*unset($ticket->ownerstate);
			unset($ticket->owner);*/
			$this->Save();
		}
		else
			echo "State error".EOL;
	}
	public function Save()
	{
		//var_dump($this->data);
		$jsondata = json_encode($this->data);
		//var_dump($jsondata);
		file_put_contents($this->loc,$jsondata);
	}
	public function ChangeState($action)
	{
		switch($action)
		{
			case ACTION_DONE_WAIT:
				$this->NextWaitState(ACTION_DONE_WAIT);
				break;
			case CANCEL_ACTION_DONE_WAIT:
				$this->PrevWaitState();
				break;
			case ACTION_DONE:
				$this->NextState();
				break;
			case ACTION_REVERT:
				$this->RevertState();
				break;
			case ACTION_REVERT_WAIT:
				$this->NextWaitState(ACTION_REVERT_WAIT);
				break;
			
		}
	}
	public function CloneUserTickets($user='all')
	{			
		$tickets = array();
		$ctstates = $this->cstates;
		
		foreach($ctstates as $stateno=>$cstate)
		{
			//echo  $stateno.EOL;
			foreach($cstate as $state)
			{
				if(strtotime($state->activated)<=strtotime(Date('Y-m-d')))
				{
					$ntask = clone $this;
					if(($state->assignee == $user)||($user=='all'))
					{
						//$ntask->owner = $state->assignee;
						//$ntask->ownerstate = $state;
						$ntask->ownerstatenumber = $stateno;
						$tickets[] = $ntask;
					}
				}
			}
		}
		return $tickets;
	}
	public function CloneAllUserTickets($user='all')
	{			
		$tickets = array();
		$ctstates = $this->cstates;
		
		foreach($ctstates as $stateno=>$cstate)
		{
			//echo  $stateno.EOL;
			foreach($cstate as $state)
			{
				if(1)
				{
					$ntask = clone $this;
					if(($state->assignee == $user)||($user=='all'))
					{
						//$ntask->owner = $state->assignee;
						//$ntask->ownerstate = $state;
						$ntask->ownerstatenumber = $stateno;
						$tickets[] = $ntask;
					}
				}
			}
		}
		return $tickets;
	}
	public function toString()
	{
		echo $this->title.EOL;
		if($this->ownerstatenumber >= 0)
		{
			echo "User Cloned Ticket".EOL;
			echo "Owner is ".$this->owner.EOL;
			echo "Ticket state is ".$this->ownerstatenumber.EOL;
			echo "Activated on ".$this->ownerstate->activated.EOL;
			echo "Activated by ".$this->ownerstate->activatedby.EOL;
			if($this->waitstatetime != false)
				echo "In Time wait state = ".$this->waitstatetime.EOL;
			echo "Instance count ".$this->instancecount.EOL;
			
		}
		else
		{
			echo "Genric Ticket".EOL;
			foreach($this->cstates as $state)
			{
				echo $state->assignee.EOL;	
				echo $state->activated.EOL;
				echo $state->days.EOL;
			}
		}
		//echo "Assigned to ".$ticket->.EOL;
	}
}
class Tickets
{
	private $list =  array();
	function __construct()
	{
		if(file_exists(TICKETS_DIR))
			$this->ReadTickets(TICKETS_DIR);
	}
	private function ReadTickets($dir)
	{
		$dirlist = scandir($dir);
		unset($dirlist[array_search('.', $dirlist, true)]);
		unset($dirlist[array_search('..', $dirlist, true)]);
		foreach($dirlist as $file)
		{
			if(is_dir($dir.'/'.$file))
			{
				$this->ReadTickets($dir.'/'.$file);
			}
			else
			{
				$ticket_file=$dir."/".$file;
				if(strpos($ticket_file,'.done')!=FALSE)
					continue;
		
				$ticket = new Ticket($ticket_file);
				$this->list[] = $ticket;
			}
		}
	}
	public function toString()
	{
		foreach($this->list as $ticket)
		{
			echo $ticket->toString();
		}
	}
	public function GetAllTickets($user='all')
	{
		$usertickets = array();
		foreach($this->list as $ticket)
		{
			$tkts = $ticket->CloneAllUserTickets($user);
			foreach($tkts as $t)
			{
				//var_dump($t);
				$usertickets[] = $t;
			}
		}
		return $usertickets;
	}
	public function GetActiveTickets($user='all')
	{
		$usertickets = array();
		foreach($this->list as $ticket)
		{
			$tkts = $ticket->CloneUserTickets($user);
			foreach($tkts as $t)
			{
				//var_dump($t);
				$usertickets[] = $t;
			}
		}
		return $usertickets;
	}
	public function DoTimeWaitStates($user='all')
	{
		$usertickets = array();
		foreach($this->list as $ticket)
		{
			$tkts = $ticket->CloneUserTickets($user);
			foreach($tkts as $t)
			{
				//var_dump($t);
				$wst = $t->waitstatetime;
				if($wst != false)
				{
					$diff = strtotime("now") - $wst;
					if($diff > 30)
					{
						//echo "changing state";
						if($t->nextaction == 'revert')
							$t->ChangeState(ACTION_REVERT);
						else
							$t->ChangeState(ACTION_DONE);
					}
					//echo $diff.EOL;
				}
			}
		}
		return $usertickets;
	}
}

?>