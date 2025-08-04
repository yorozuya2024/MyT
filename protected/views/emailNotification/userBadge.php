<?php
/** @var $this EmailNotification */
/** @var $user User */
/** @var $title string */
$resize = array(
    'resize' => array(
        'width' => Yii::app()->params['imageDimension']['maxWidthThumb'] * 1.1,
//        'height' => Yii::app()->params['imageDimension']['maxHeightThumb']
    )
);
$gender = $user->gender === 'F' ? 'F' : 'M';
$avatar = $user->avatar ? Yii::app()->params['avatarPath'] . $user->avatar : Yii::app()->params['avatarPath'] . 'default_avatar_' . $gender . '.jpg';

$img = Yii::app()->getBaseUrl(true) . '/' . Yii::app()->easyImage->thumbSrcOf($avatar, $resize);
?>
<table width="100%" cellpadding="2" cellspacing="2" border-collapse="collapse">
  <?php if ($title !== null): ?>
    <tr>
      <td bgcolor="#6faccf" colspan="2" style="font-size: 11pt; color: #FFFFFF;">&nbsp;<b><?php echo $title; ?></b></td>
    </tr>
  <?php endif; ?>
  <tr bgcolor="#e6eff3">
    <td width="<?php echo $resize['resize']['width']; ?>">
      <img src="<?php echo $img; ?>" alt="<?php echo $user->calc_name; ?>" title="<?php echo $user->calc_name; ?>" />
    </td>
    <td>
      <b><?php echo $user->calc_name; ?></b>
      <br />
      <?php echo $user->username; ?>
      <br />
      <a href="mailto:<?php echo $user->email; ?>"><?php echo $user->email; ?></a>
    </td>
  </tr>
</table>