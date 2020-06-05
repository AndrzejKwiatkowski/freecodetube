<?php
namespace frontend\controllers;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\models\Video;
use common\models\VideoView;
use yii\web\NotFoundHttpException;

class VideoController extends Controller

{
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Video::find()->published()->latest()
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }
    public function actionView($id)
    {
        $this->layout = 'auth';
        $video = Video::findOne($id);
        if(!$video){
            throw new NotFoundHttpException("Video dose not exist");
         
        }

        $viedoView = new VideoView();
        $viedoView->video_id = $id;
        $viedoView->user_id = \Yii::$app->user->id;
        $viedoView->created_at = time();
        $viedoView->save();
        return $this->render('view', [
            'model' => $video
        ]);
    }
}