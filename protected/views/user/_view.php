<?php
/* @var $this UserController */
/* @var $data User */
?>

<div class="view">
  <table style="margin:0">
    <tr>
      <td>
        <b class="span-3"><?php echo CHtml::encode($data->getAttributeLabel('username')); ?></b>
        <span><?php echo CHtml::link(CHtml::encode($data->username), array('view', 'id' => $data->id)); ?></span>
        <br />
        <b class="span-3"><?php echo CHtml::encode($data->getAttributeLabel('email')); ?></b>
        <span><?php echo CHtml::mailto(CHtml::encode($data->email)); ?></span>
      </td>
      <td class="right">
        <?php
        if ($data->avatar) {
          $resize = array(
              'resize' => array(
                  'width' => Yii::app()->params['imageDimension']['maxWidthThumb'],
//                            'height' => Yii::app()->params['imageDimension']['maxHeightThumb']
              )
          );
          echo Yii::app()->easyImage->thumbOf(Yii::app()->params['avatarPath'] . $data->avatar, $resize);
        }
        ?>
      </td>
    </tr>
  </table>
</div>