<?php
/**
 * @var $model \common\models\video
 */
?>
<div class="row">
    <div class="col-sm-8">
        <div class="embed-responsive embed-responsive-16by9">
                    <video class="embed-responsive-item"
                    poster="<?php echo $model->getThumbnailLink() ?>" 
                    src="<?php echo $model->getVideoLink() ?>" 
                    controls></video>
        </div>
        <h6 class="mt-2"><?php echo $model->title ?></h6>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <?php echo $model->getViews()->count() ?> • views
                <?php echo Yii::$app->formatter->asDate($model->created_at)?>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-primary">9
                <i class="fas fa-thumbs-up"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary">3
                <i class="fas fa-thumbs-down"></i>
                </button>
            </div>
        </div>
        
    </div>
    <div class="col-sm-4">

    </div>
</div>
