<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$title = Yii::t('nav', 'Login');
$this->pageTitle = Yii::app()->name . ' - ' . $title;
//$this->breadcrumbs=array(
//	$title,
//);
?>
<hr class="hr-separator">
<h1><b><?php echo 'プロジェクト管理MyT(Manage Your Team)'; ?></b></h1>
<table>
<tr valign="top">
<td>
<h2><b><?php echo $title; ?></b></h2>
<p></p>
<?php if (Yii::app()->user->hasFlash('passwordchanged')): ?>

  <div class="flash-success">
    <?php echo Yii::app()->user->getFlash('passwordchanged'); ?>
  </div>

<?php endif; ?>

<div class="form">
  <?php
  $form = $this->beginWidget('CActiveForm', array(
      'id' => 'login-form',
      'enableClientValidation' => true,
      'clientOptions' => array(
          'validateOnSubmit' => true,
      ),
  ));
  ?>

  <div class="row">
    <?php echo $form->labelEx($model, 'username'); ?>
    <?php echo $form->textField($model, 'username'); ?>
    <?php echo $form->error($model, 'username'); ?>
  </div>

  <div class="row">
    <?php echo $form->labelEx($model, 'password'); ?>
    <?php echo $form->passwordField($model, 'password'); ?>
    <?php echo $form->error($model, 'password'); ?>
    <p class="hint">
      <?php echo CHtml::link(Yii::t('app', 'LoginForm.password.forgotten'), array('user/resetpassword')); ?>
    </p>
  </div>

  <div class="row rememberMe">
    <?php echo $form->checkBox($model, 'rememberMe'); ?>
    <?php echo $form->label($model, 'rememberMe'); ?>
    <?php echo $form->error($model, 'rememberMe'); ?>
  </div>

  <div class="row buttons">
    <?php echo CHtml::submitButton(Yii::t('app', 'Form.login')); ?>
    <!-- 2024/9/13 add -->
    <?php
 //echo '<br />' . CHtml::link(Yii::t('app', 'registration'), array('registration/register')); 
echo '<br />' . CHtml::Button('新規会員登録', array(
    'onclick' => 'window.location.href="' . CHtml::normalizeUrl(array('registration/register')) . '";'
)); 
?>
  </div>
</td>
</tr>
<tr valign="top">
<td>
<h2><b>チーム管理をもっとスマートに、MyTで。</b></h2>
<p></p>
<p>MyT (Manage Your Team) は、チームのタスクやプロジェクトを効率的に管理できる、</p>
<p>シンプルかつ強力なツールです。直感的な操作で、進捗状況を一目で把握でき、</p>
<p>スムーズなワークフローをサポートします。</p>
<p></p>
<h2><b>MyTとは？</b></h2>
<p></p>
<p>MyTは、Yii Frameworkに基づいた無料のオープンソースのタスク・プロジェクト管理システムです。</p>
<p>シンプルで使いやすく、拡張性にも優れており、チームの成長に合わせた柔軟な運用が可能です。</p>
<p></p>
<h2><b>主な機能</b></h2>
<p></p>
<p>タスク管理: 複数のプロジェクトやタスクを一括管理</p>
<p>バグ追跡: プロジェクトの問題点を迅速に把握</p>
<p>タイムシート管理: 作業時間を正確に記録</p>
<p>ドキュメント管理: チーム内でのファイル共有と整理</p>
<p>スマホ対応: モバイルデバイスからもアクセス可能</p>
<p>誰でも使える直感的なインターフェースで、リアルタイムで進捗確認やタスクの管理が可能。</p>
<p>チーム全体の協力を促し、コミュニケーションを円滑にします。</p>
<p></p>
<p>今すぐMyTにログインして、より効率的なチーム管理を始めましょう！</p>
<p></p>
<h2><b>お問い合わせ</b></h2>
<p></p>
<p>MyTのシステム構築ついてはお気軽にお問い合わせください。</p>
<p><a href="mailto:nexus6user2023132432@gmail.com">お問い合わせ</a></p>
</td>
</tr>
</table>

  <?php $this->endWidget(); ?>
</div>
