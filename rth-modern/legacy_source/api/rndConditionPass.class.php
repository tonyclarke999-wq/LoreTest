<?php
/************************************************************************/
/* rndConditionPass.class.php                                           */
/* =====================================================================*/
/* Copyright (c) 2005 by Gobinath  (gobinathm at gmail dot com)         */
/*                                                                      */
/* Author(s): Gobinath                                                  */
/* =====================================================================*/
/* Title:                                                               */
/*      Random Password Generating Class take some Conditions when      */
/*       generating the Password                                        */
/*   Date: April 27th, 2005                                             */
/* =====================================================================*/

/*
example

$condition = array('caps'=>2, 'small'=>3, 'nums'=>2, 'specs'=>2);
$pass=new rndConditionPass(7,$condition);
$tmppass=$pass->PassGen();
echo $tmppass;

*/

// Class Declaration Starts:
class rndConditionPass{

	var $PasswordLength;    // Variable To Assign Password Length
	var $caps = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var $small= 'abcdefghjkmnpqrstuvwxyz';
	var $nums = '0123456789';
    var $specs= '+-*&$#@!';   // This Can be removed if not Needed
	var $condition;
	var $minLen;

	function rndConditionPass($passLen,$condition){ //Constructor To Assign Values
		$this->condition = $condition;  // Will Store the Condition Array to Global Variable
		// Will calculate the Minimum Length
		$this->minLen = $this->condition['caps']+$this->condition['small']+$this->condition['nums']+$this->condition['specs'];
		// Compute the Total Password Length and Store it to the Global Variable
		$this->PasswordLength = max($this->minLen, $passLen);
	}

	function PassGen(){ // Function to Generate Random Passowrd
		$i = 1;
		$password = '';
		while ($i < $this->PasswordLength) {
		       if($i < $this->minLen){
				    if ($i<$this->condition['specs']) $this->set = $this->specs;
					elseif ($i<($this->condition['specs']+$this->condition['small'])) $this->set = $this->small;
					elseif ($i<($this->condition['specs']+$this->condition['small']+$this->condition['nums'])) $this->set = $this->nums;
			 		elseif ($i<($this->condition['specs']+$this->condition['small']+$this->condition['nums']+$this->condition['caps'])) $this->set = $this->caps;
		 	    	elseif ($i<($this->condition['specs']+$this->condition['small']+$this->condition['nums']+$this->condition['specs'])) $this->set = $this->specs;
  		        }else{
					if ($i<$this->PasswordLength) $this->set = $this->all;
   	            }
			$tmp = $this->_getPwdChar($this->set);
			$password .= $tmp;
			$i++;
		}
		return str_shuffle($password);
	}

	function PassCheck($pass) {   // Function To Check Whether the Password have those Conditions
		$cond = array('caps'=>0, 'small'=>0, 'nums'=>0, 'specs'=>0);
		for ($i=0;$i<strlen($pass);$i++) {
			$c = substr($pass, $i, 1);
			if (strpos($this->caps, $c)) { $cond['caps']++; }
			if (strpos($this->small, $c)) { $cond['small']++; }
			if (strpos($this->nums, $c)) { $cond['nums']++; }
			if (strpos($this->specs, $c)) { $cond['specs']++; }
		}
		if ($this->condition['caps']<=$cond['caps'] && $this->condition['small']<=$cond['small'] && $this->condition['nums']<=$cond['nums'] && $this->condition['specs']<=$cond['specs']) {
			return true;
		} else {
			return false;
		}
	}

	function _getPwdChar($set) {
		mt_getrandmax();  // Returns the maximum value that can be returned by a call  rand
		$num = rand() % strlen($set);
		$tmp = substr($set, $num, 1);
		return $tmp;
	}
}
?>