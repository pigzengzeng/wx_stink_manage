<?php
class ErrorCode{
	public static $OK = 0;
	
	public static $ParamError = -1; //参数错误
	public static $IllegalJsonString = -2; //无效json
	public static $IllegalRequest = -3; //无效请求
	public static $IllegalUser = -4; //无效用户
	public static $PwdError = -5; //密码错
	public static $PermissionDenied = -6; //没有权限
	public static $DbError = -10;
	public static $DbEmpty = -11;
	
}
