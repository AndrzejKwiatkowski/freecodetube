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
                    'only' => ['like', 'dislike', 'history'],
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
        $this->layout = 'main';
        $dataProvider = new ActiveDataProvider([
            'query' => Video::find()->with('createdBy')->published()->latest(),
            'pagination' => [
                'pageSize' => 4
            ]
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

        $similarVideos = Video::find()
                ->published()
                ->byKeyword($video->title)
                ->andWhere(['NOT', ['video_id' => $id]])
                ->limit(10)
                ->all();

        return $this->render('view', [
            'model' => $video,
            'similarVideos' => $similarVideos
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
    public function actionSearch($keyword)
    {
        $this->layout = 'main';
        $query = Video::find()
            ->with('createdBy')
            ->published()
            ->latest();
            

        if($keyword){
            $query->byKeyword($keyword);
            
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $this->render('search', [
            'dataProvider' => $dataProvider
        ]);
    }
    public function actionHistory()
    {
        $this->layout = 'main';
        $query = Video::find()
        ->alias('v')
        ->innerJoin("(SELECT video_id, MAX(created_at) as max_data FROM video_view
        where user_id = :userId
        GROUP BY video_id) vv", 'vv.video_id = v.video_id', [
            'userId' => \Yii::$app->user->id
        ])
        ->orderBy("vv.max_data DESC");
        
    
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        return $this->render('history', [
            'dataProvider' => $dataProvider
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