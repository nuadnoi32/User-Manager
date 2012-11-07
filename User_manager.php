<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User Class
 *
 * @package		---
 * @subpackage	Libraries
 * @category	Users
 * @author		Nuadnoi
 * @link		---
 */

class User_manager{

	private $_CI;
	
	public function __construct(){
		$this->_CI =& get_instance();
	}
	
	/*
	 * Register function
	 * 
	 * function register($username,$password,$arr_data[,$arr_data_require = NULL])
	 * 
	 * @username			string
	 * @password			string
	 * @arr_data			array
	 * @arr_data_require	array (Optional)
	 * @return 				TRUE / FALSE
	 * 
	 */
	public function register($username,$password,$arr_data,$arr_data_require = NULL){
		
			if(isset($arr_data_require)){
				if(is_array($arr_data_require) && sizeof($arr_data_require) !== 0){
					foreach($arr_data_require as $key => $data_require){
						$require[$key] = FALSE;
						if(empty($data_require)){
							$require[$key] = TRUE;
						}
					}
					
					$show_require = '';
					foreach($require as $key => $val){
						if($val === TRUE){
							$show_require .= $key.',';
						}
					}
					
					if(!empty($show_require)){
						show_error('Error occured, "'.$show_require.'" is required!');
						exit();
					}
				}else{
					return FALSE;	
				}
			}
			
			if(is_array($arr_data) && sizeof($arr_data) !== 0){
				$this->_CI->db->select($username);
				$query = $this->_CI->db->get_where(_TABLE_MEMBER_,array($username => $arr_data[$username]));
				if($query->num_rows() === 1){
					show_error('Error occured, The Username is currently used!');
					exit();
				}else{
					$arr_data[$password] = $this->hashPassword($arr_data[$password], $arr_data[$username]);
					$this->_CI->db->insert(_TABLE_MEMBER_,$arr_data);
					return TRUE;
				}
			}else{
				return FALSE;
			}
		
	}
	
	/*
	 * Hash the password
	 * 
	 * @param	string
	 * @return	string
	 */
	public function hashPassword($password,$username){
		$hash = crypt($password, $this->generateSalt($username));
		return $hash;
	}
	
	/*
	 * Create general SHA1&MD5 hashing by using username
	 * 
	 * @param	string
	 * @return	string
	 */
	public function generateSalt($username){
		$salt = sha1(md5(strtolower($username)));
		return $salt;
	}
}