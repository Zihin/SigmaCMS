<?php

/**
 * Created by PhpStorm.
 * User: blackcater
 * Date: 16/8/2
 * Time: 下午4:40
 */
class News extends API_Middleware
{
    public function __construct()
    {
        parent::__construct();
    }

    public function news_get() {
        $id = $this->get('id');
        $type = $this->get('type');

        if (!isset($id)) {
            // 获得全部活动
            $this->load->model('News_model', 'newsModel');
            $news = $this->newsModel->getAllNews();
            if (empty($news)) {
                $this->response([
                    'status' => false,
                    'code' => REST_Controller::HTTP_NOT_FOUND,
                    'error' => 'Can\'t find any activities!'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->response([
                    'status' => true,
                    'code' => REST_Controller::HTTP_OK,
                    'data' => $news
                ], REST_Controller::HTTP_OK);
            }
        }

        if (!isset($type)) {
            // 获得特定id的活动内容
            $this->load->model('News_model', 'newsModel');
            $news = $this->newsModel->getNewsById($id);

            if (empty($news)) {
                $this->response([
                    'status' => false,
                    'code' => REST_Controller::HTTP_NOT_FOUND,
                    'error' => 'Can\'t find the activity!'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->response([
                    'status' => true,
                    'code' => REST_Controller::HTTP_OK,
                    'data' => $news
                ], REST_Controller::HTTP_OK);
            }
        }

        if ($type === 'comment') {
            // 获取特定id下所有评论
            $this->load->model('NewsComment_model', 'ncModel');
            $comments = $this->ncModel->getAllCommentsByNewsId($id);

            if (empty($comments)) {
                $this->response([
                    'status' => false,
                    'code' => REST_Controller::HTTP_NOT_FOUND,
                    'error' => 'Can\'t find any comment!'
                ], REST_Controller::HTTP_NOT_FOUND);
            } else {
                $this->response([
                    'status' => true,
                    'code' => REST_Controller::HTTP_OK,
                    'data' => $comments
                ], REST_Controller::HTTP_OK);
            }
        } else {
            $this->response([
                'status' => false,
                'code' => REST_Controller::HTTP_BAD_REQUEST,
                'error' => 'Invalid API'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }

    }
}