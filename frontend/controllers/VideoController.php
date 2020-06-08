<?php
namespace frontend\controllers;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\models\Video;
use common\models\VideoView;
use common\models\VideoLike;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class VideoController extends Controller

{
    

    public function behaviors()
    {
        
            return [
                'access' => [
                    'class' => AccessControl::class,
                    'only' => ['like', 'dislike'],
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['@']
                        ]
                    ]
                ],
                'verb' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'like' => ['post'],
                        'dislike' => ['post'],
                    ]
                ]
            ];
    }
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
        $video = $this->findVideo($id);

        $viedoView = new VideoView();
        $viedoView->video_id = $id;
        $viedoView->user_id = \Yii::$app->user->id;
        $viedoView->created_at = time();
        $viedoView->save();
        return $this->render('view', [
            'model' => $video
        ]);
    }
    public function actionLike($id)
    {
        $video = $this->findVideo($id);
        $userId =\Yii::$app->user->id;

        $videoLikeDislike = VideoLike::find()
        ->userIdVideoId($userId, $id)
        ->one();
        if(!$videoLikeDislike) {
            $this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
       
        } else if ($videoLikeDislike->type == VideoLike::TYPE_LIKE){
            $videoLikeDislike->delete();
        } else {
            $videoLikeDislike->delete();
            $this->saveLikeDislike($id, $userId, VideoLike::TYPE_LIKE);
        }
        return $this->renderAjax('_buttons', [
            'model' => $video
        ]);
   
    }
    public function actionDislike($id)
    {
        $video = $this->findVideo($id);
        $userId =\Yii::$app->user->id;

        $videoLikeDislike = VideoLike::find()
        ->userIdVideoId($userId, $id)
        ->one();
        if(!$videoLikeDislike) {
            $this->saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
       
        } else if ($videoLikeDislike->type == VideoLike::TYPE_DISLIKE){
            $videoLikeDislike->delete();
        } else {
            $videoLikeDislike->delete();
            $this->saveLikeDislike($id, $userId, VideoLike::TYPE_DISLIKE);
        }
        return $this->renderAjax('_buttons', [
            'model' => $video
        ]);
   
    }
    
    protected function findVideo($id)
    {
        $video = Video::findOne($id);
        if(!$video){
            throw new NotFoundHttpException("Video dose not exist");
        }
        return $video;
    }
    protected function saveLikeDislike($videoId, $userId, $type){
        
        $videoLikeDislike = new VideoLike();
        $videoLikeDislike->video_id = $videoId;
        $videoLikeDislike->user_id = $userId;
        $videoLikeDislike->type = $type;
        $videoLikeDislike->created_at = time();
        $videoLikeDislike->save();
    }
}