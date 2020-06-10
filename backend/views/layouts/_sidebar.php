<aside class="shadow">
    <?php echo yii\bootstrap4\Nav::widget([
        'options' => [
            'class' => ['d-flex flex-column nav-pills']
        ],
        'encodeLabels' => false,
        'items' => [
            [
                'label' => '<i class="fas fa-columns"></i> Dashboard',
                'url' => ['/site/index']
            ],
            [
                'label' => '<i class="fas fa-video"></i> Video',
                'url' => ['/video/index']
            ],
        ]
    ]) ?>
</aside>