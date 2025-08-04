<?php
class CheckUpdateController extends Controller
{
  public $layout = '//layouts/column1';

  /**
   * @return array action filters
   */
  public function filters() {
    return array(
        'accessControl', // perform access control for CRUD operations
    );
  }

  /**
   * Specifies the access control rules.
   * This method is used by the 'accessControl' filter.
   * @return array access control rules
   */
  public function accessRules() {
    return array(
        array('allow', // allow all users to perform 'index' and 'view' actions
            'actions' => array('index'),
            'users' => array('@'),
            'roles' => array('adminConfig')
        ),
        array('deny', // deny all users
            'users' => array('*'),
        ),
    );
  }
    
  public function actionIndex()
  {
	$url = 'http://manageyourteam.net/versionCheck.php';
	
	$xml = $this->_getRemoteFile( $url );
	
	$versInfo = array();
	
	if( $xml )
	{
		$xmlObj = simplexml_load_string( $xml );
		
		$versInfo['last_version']	= $xmlObj->myt->last_version;
		$versInfo['download_url']	= $xmlObj->myt->download_url;
		$versInfo['release_date']	= $xmlObj->myt->release_date;
		$versInfo['type']			= $xmlObj->myt->type;
		$versInfo['your_version']	= Yii::app()->params['mytVersion'];
		
		if( version_compare( $xmlObj->myt->last_version, Yii::app()->params['mytVersion'], '>' ) )
		{
			 Yii::app()->user->setFlash('error', Yii::t('app', 'CheckUpdate.check.newversion.{lastVersion}.{url}',
										array('{lastVersion}' => $versInfo['last_version'], '{url}' => $versInfo['download_url'])));
		}
		else
		{
			Yii::app()->user->setFlash('success', Yii::t('app', 'CheckUpdate.check.nonewversion'));
		}
			
	}
	else
	{
      Yii::app()->user->setFlash('error', Yii::t('app', 'CheckUpdate.remote.error'));
	}
	
	$this->render('index', array( 'version' => $versInfo ));
	
  }
  
  private function _getRemoteFile( $url )
  {
	$content = null;
	
	if( ini_get( 'allow_url_fopen' ) )
	{
		$content = @file_get_contents( $url );
	}
	else if ( function_exists( 'curl_init' ) )
	{
		$options = array( CURLOPT_URL => $url,
						  CURLOPT_HEADER => 0,
						  CURLOPT_TIMEOUT => 10,
						  CURLOPT_RETURNTRANSFER => 1 );
						  
		$ch = curl_init();
		curl_setopt_array( $ch, $options );
		$content = curl_exec( $ch );
		curl_close($ch);
		
	}
	return $content;
  }
}