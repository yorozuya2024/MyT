<?php

/**
 * Description of EmailNotification
 *
 * @author francesco.colamonici
 */
class EmailNotification extends CController {

  public function __construct() {
    parent::__construct('emailNotification');
  }

  private function getMessage() {
    Yii::import('ext.yii-mail.YiiMailMessage');
    $transport = Yii::app()->request->getUserHostAddress() === '127.0.0.1' ? 'php' : Yii::app()->params['paramsEmail']['method'];
    Yii::app()->mail->transportType = $transport;
    if ($transport === 'smtp')
      Yii::app()->mail->transportOptions = Yii::app()->params['paramsEmail']['smtp'];
    return new YiiMailMessage();
  }

  /**
   * Sends a Task Association notification email.
   * @param array $users To be notified User Ids
   * @param string $taskId Task Id
   */
  public function sendTaskAssociationNotification(array $users, $taskId) {
    $enable = CPropertyValue::ensureBoolean(Yii::app()->params['notifications']['taskAssociation']['email']);
    if ($enable) {
      // 2024/9/20 debug
      return;
      $yiiMessage = $this->getMessage();
      $message = $yiiMessage->message;

      $message->setFrom(Yii::app()->params['adminEmail'], Yii::app()->params['name']);
      $message->setReplyTo(Yii::app()->params['adminEmail'], Yii::app()->params['name']);

      $task = Task::model()->findByPk($taskId);
      $header = $task->project->name . ' - ' . $task->title . ' - [' . $task->calc_id . '] - [' . $task->getStatus() . ']';
      $message->setSubject(Yii::app()->params['subjectPrefixEmail'] . $header);

      $creator = $this->renderInternal($this->getViewFile('userBadge'), array('user' => $task->creator, 'title' => Yii::t('attributes', 'Task.created_by')), true);
      $taskDetails = $this->renderInternal($this->getViewFile('taskBadge'), array('task' => $task, 'title' => Yii::t('app', 'Task.form.details')), true);

      foreach ($users as $userId) {
        $user = User::model()->findByPk($userId);
        if ($user->getNotification('taskAssociation', 'email')) {
          $owner = $this->renderInternal($this->getViewFile('userBadge'), array('user' => $user, 'title' => Yii::t('app', 'Task.form.assignment')), true);

          $body = $this->renderInternal($this->getViewFile('taskEmailBody'), array(
              'task' => $task,
              'header' => $header,
              'creator' => $creator,
              'owner' => $owner,
              'taskDetails' => $taskDetails,
                  ), true);
          $message->setTo($user->email, $user->calc_name);
          $message->setBody($body, 'text/html');
          Yii::app()->mail->send($yiiMessage);
        }
      }
    }
  }

  public function sendProjectAssociationNotification(array $users, $projectId) {
    $enable = CPropertyValue::ensureBoolean(Yii::app()->params['notifications']['projectAssociation']['email']);
    if ($enable) {
      // 2024/9/20 debug
      return;
      $yiiMessage = $this->getMessage();
      $message = $yiiMessage->message;

      $message->setFrom(Yii::app()->params['adminEmail'], Yii::app()->params['name']);
      $message->setReplyTo(Yii::app()->params['adminEmail'], Yii::app()->params['name']);

      $project = Project::model()->findByPk($projectId);
      $header = Yii::t('app', 'Project.email.assignment.title');
      $message->setSubject(Yii::app()->params['subjectPrefixEmail'] . $header);

//            $champion = $this->renderInternal($this->getViewFile('userBadge'), array('user' => $project->championBadge, 'title' => 'Project Champion'), true);
      $details = $this->renderInternal($this->getViewFile('projectBadge'), array('project' => $project, 'title' => Yii::t('app', 'Project.form.details')), true);

      foreach ($users as $userId) {
        $user = User::model()->findByPk($userId);
        if ($user->getNotification('projectAssociation', 'email')) {
//                    $associated = $this->renderInternal($this->getViewFile('userBadge'), array('user' => $user, 'title' => 'Associated'), true);

          $body = $this->renderInternal($this->getViewFile('projectEmailBody'), array(
              'project' => $project,
              'header' => $header,
//                        'champion' => $champion,
//                        'associated' => $associated,
              'details' => $details,
                  ), true);
          $message->setTo($user->email, $user->calc_name);
          $message->setBody($body, 'text/html');
          Yii::app()->mail->send($yiiMessage);
        }
      }
    }
  }
  
  public function sendEmail( $to, $subject, $body, $type = 'html' )
  {
	$yiiMessage = $this->getMessage();
	$yiiMessage->message->setFrom( Yii::app()->params['adminEmail'], Yii::app()->params['name'] );
	$yiiMessage->message->setReplyTo( Yii::app()->params['adminEmail'], Yii::app()->params['name'] );
	$yiiMessage->message->setTo( $to );
	$yiiMessage->message->setSubject( $subject );
	$yiiMessage->message->setBody( $body, $type == 'html' ? 'text/html' : 'text/plain' );
	
	Yii::app()->mail->send( $yiiMessage );
  }

}
