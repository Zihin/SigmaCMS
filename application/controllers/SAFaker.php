<?php

/**
 * Created by PhpStorm.
 * User: blackcater
 * Date: 16/7/16
 * Time: 下午11:23
 */
class SAFaker extends CI_Controller
{
    protected $faker;

    static protected $userId = 1;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('SAFaker_model', 'fakerModel');
        $this->load->model('School_model', 'schoolModel');
        $this->config->load('config');
        $this->faker = Faker\Factory::create('zh_CN');
    }

    /**
     * faker city数据
     */
    public function faker_city()
    {
        // 载入数据
        $filePath = ROOTPATH . 'city.xml';
        $xml = simplexml_load_file($filePath);
        $index = 1;
        $data = [];
//        print_r($xml);
        foreach ($xml->root->row as $row) {
            $key = (string)$row->key;
            foreach ($row->array->string as $item) {
                $data[] = [
                    'code' => $index,
                    'name' => (string)$item,
                    'key' => $key
                ];
                $index++;
            }
        }

        // 将数据添加到数据库
        if ($this->fakerModel->addFakerCity($data)) {
            echo 'City数据添加成功!';
        } else {
            echo 'City数据添加失败!';
        }
    }

    /**
     * faker school数据
     */
    public function faker_school()
    {
        // 载入数据
        $filePath = ROOTPATH . 'school.xml';
        $xml = simplexml_load_file($filePath);
        $data = [];
        foreach ($xml->Root->Row as $row) {
            $code = (int)$row->Cell[0]->Data;
            $name = (string)$row->Cell[1]->Data;
            $city_code = (int)$row->Cell[3]->Data;
            if ($city_code !== 0) {
                $data[] = [
                    'code' => $code,
                    'name' => $name,
                    'city_code' => $city_code
                ];
            }
        }


        if ($this->fakerModel->addFakerSchool($data)) {
            echo 'School数据添加成功!';
        } else {
            echo 'School数据添加失败!';
        }
    }

    /**
     * faker user type数据
     */
    public function faker_userType()
    {
        $data = [
            [
                'code' => 1,
                'name' => '学生'
            ],
            [
                'code' => 2,
                'name' => '老师'
            ],
            [
                'code' => 3,
                'name' => '竞赛组委会'
            ],
            [
                'code' => 4,
                'name' => '高校活动方'
            ],
            [
                'code' => 5,
                'name' => '高校代理点'
            ]
        ];

        if ($this->fakerModel->addFakerUserType($data)) {
            echo 'UserType添加成功!';
        } else {
            echo 'UserType添加失败!';
        }
    }

    /**
     * faker user social数据
     */
    public function faker_userSocial()
    {
        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $is_qq = $this->faker->randomElement([0, 1]);
            if ($is_qq) {
                $qq = $this->faker->regexify('[1-9][0-9]{8,11}');
            } else {
                $qq = '';
            }
            $is_weibo = $this->faker->randomElement([0, 1]);
            if ($is_weibo) {
                $weibo = $this->faker->regexify('[a-z]{2,6}[0-9]{4,6}');
            } else {
                $weibo = '';
            }
            $is_wechat = $this->faker->randomElement([0, 1]);
            if ($is_wechat) {
                $wechat = $this->faker->regexify('[a-z]{2,6}[0-9]{4,6}');
            } else {
                $wechat = '';
            }
            $data[] = [
                'id' => $i + 1,
                'qq' => $qq,
                'is_qq' => $is_qq,
                'weibo' => $weibo,
                'is_weibo' => $is_weibo,
                'wechat' => $wechat,
                'is_wechat' => $is_wechat
            ];
        }

        if ($this->fakerModel->addFakerUserSocial($data)) {
            echo 'User Social数据添加成功!';
        } else {
            echo 'User Social数据添加失败!';
        }
    }

    /**
     * faker user privilege 数据
     */
    public function faker_userPrivilege() {
        $data = [];
        for ($i = 0; $i < 30; $i++) {
            $id = $i+1;
            $friend_visibility = $this->faker->numberBetween(0, 2);
            $follow_visibility = $this->faker->numberBetween(0, 2);
            $sex_visibility = $this->faker->numberBetween(0, 2);
            $name_visibility = $this->faker->numberBetween(0, 2);
            $phone_visibility = $this->faker->numberBetween(0, 2);
            $email_visibility = $this->faker->numberBetween(0, 2);

            $data[] = [
                'id' => $id,
                'friend_visibility' => $friend_visibility,
                'follow_visibility' => $follow_visibility,
                'sex_visibility' => $sex_visibility,
                'name_visibility' => $name_visibility,
                'phone_visibility' => $phone_visibility,
                'email_visibility' => $email_visibility
            ];
        }

        for ($j = 0; $j < 30; $j++) {
            $this->fakerModel->updateUserUserPrivilege($j+1);
        }

        if ($this->fakerModel->addFakerUserPrivilege($data)) {
            echo 'User Privilege 数据添加成功!';
        } else {
            echo 'User Privilege 数据添加失败';
        }
    }

    /**
     * faker user 数据
     */
    public function faker_user()
    {
        $msg = '';
        // 测试 一次上传10条数据的效果
        for ($i = 17; $i < 30; $i++) {
            $data = [];
            $username_type = $this->faker->randomElement(['email', 'phone', 'customer']);
            switch ($username_type) {
                case 'email': {
                    $username = $this->faker->safeEmail;
                    $email = $username;
                    $phone = '';
                }
                    break;
                case 'phone' : {
                    $username = $this->faker->phoneNumber;
                    $phone = $username;
                    $email = '';
                }
                    break;
                case 'customer' : {
                    $username = $this->faker->regexify('[a-zA-Z0-9]{6,10}');
                    $email = '';
                    $phone = '';
                }
                    break;
            }
            $password = md5($this->config->item('si_md5') . $username);
            $nickname = $this->faker->text(15);
            $image = $this->faker->imageUrl(120, 120);
            $bgImage = $this->faker->imageUrl(750, 350);
            $signatureImage = $this->faker->imageUrl(750, 350);
            $signature = $this->faker->text(60);
            $point = $this->faker->randomNumber(4);
            $coin = $this->faker->randomNumber(4);
            $user_level = $this->faker->randomNumber(2);
            $school_code = $this->faker->numberBetween(1, 2553);
            while (!$this->schoolModel->getSchoolWithCode($school_code)) {
                // 判断是否为有效的school_code
                $school_code = $this->faker->numberBetween(1, 2553);
            }
            $city_code = $this->faker->numberBetween(1, 216);
            $user_type = $this->faker->numberBetween(1, 2);
            $user_social = $i + 1;
            $last_login_city = $this->faker->numberBetween(1, 216);
            $last_login_date = $this->faker->unixTime('now');
            $last_register_date = $this->faker->unixTime($last_login_date);
            $is_active = 1;
            $active_date = $this->faker->unixTime($last_register_date);
            $apply_date = 1800;
            $apply_code = $this->faker->regexify('[0-9A-Z]{6}');
            $data[] = [
                'id' => $i + 1,
                'username' => $username,
                'password' => $password,
                'nickname' => $nickname,
                'username_type' => $username_type,
                'email' => $email,
                'phone' => $phone,
                'bgImage' => $bgImage,
                'signature' => $signature,
                'signatureImage' => $signatureImage,
                'point' => $point,
                'coin' => $coin,
                'user_level' => $user_level,
                'school_code' => $school_code,
                'city_code' => $city_code,
                'user_type' => $user_type,
                'user_social' => $user_social,
                'last_login_city' => $last_login_city,
                'last_login_date' => $last_login_date,
                'last_register_date' => $last_register_date,
                'is_active' => $is_active,
                'active_date' => $active_date,
                'apply_date' => $apply_date,
                'apply_code' => $apply_code
            ];

            if ($this->fakerModel->addFakerUser($data)) {
                // 拉取图片
                $res1 = upload_file_to_qiniu(download_file_by_curl($image), 'user', 'image', $i + 1);
                $res2 = upload_file_to_qiniu(download_file_by_curl($bgImage), 'user', 'bgImage', $i + 1);
                $res3 = upload_file_to_qiniu(download_file_by_curl($signatureImage), 'user', 'signatureImage', $i + 1);

                if ($res1 && $res2 && $res3) {
                    $msg .= ($i + 1) . ' User 数据上传成功!' . '<br/>';
                } else {
                    $msg .= 'User 数据上传失败!' . '<br/>';
                }
            } else {
                $msg .= 'User 数据添加失败!' . '<br/>';
            }
        }

        echo $msg;
    }

    /**
     * faker advertisement 数据
     */
    public function faker_advertisement()
    {
        $msg = '';
        for ($i = 0; $i < 50; $i++) {
            $data = [];
            $id = $i + 1;
            $url = $this->faker->imageUrl(750, 350);
            $level = $this->faker->numberBetween(1, 10);
            $time = mktime(12, 0, 0, 10, 20, 2020);
            $e_date = $this->faker->unixTime($time);
            $s_date = $this->faker->unixTime($e_date);

            $data[] = [
                'id' => $id,
                'url' => $url,
                'level' => $level,
                's_date' => $s_date,
                'e_date' => $e_date
            ];

            if ($this->fakerModel->addFakerAdvertisement($data)) {
                $res = upload_file_to_qiniu(download_file_by_curl($url), 'advertisement', 'url', $id);
                if ($res) {
                    $msg .= 'Advertisement 数据添加成功!' . '<br/>';
                } else {
                    $msg .= 'Advertisement 数据添加失败!' . '<br/>';
                }
            } else {
                $msg .= 'Advertisement 数据添加失败!' . '<br/>';
            }
        }

        echo $msg;
    }

    /**
     * faker question 数据
     */
    public function faker_question()
    {
        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $id = $i + 1;
            $title = $this->faker->text(30);
            $user_id = $this->faker->numberBetween(1, 30);
            $url = '';
            $duration = 0;
            $pay_type = $this->faker->randomElement([1, 2]);
            $pay_num = $this->faker->randomElement([1, 5, 10, 20, 50, 100]);
            $is_free = $this->faker->randomElement([0, 1]);
            $look = $this->faker->numberBetween(1, 1000);
            $save = $this->faker->numberBetween(1, 100);
            $praise = $this->faker->numberBetween(1, 100);
            $last_look_date = $this->faker->unixTime('now');
            $publish_date = $this->faker->unixTime($last_look_date);

            $data[] = [
                'id' => $id,
                'title' => $title,
                'user_id' => $user_id,
                'url' => $url,
                'duration' => $duration,
                'pay_type' => $pay_type,
                'pay_num' => $pay_num,
                'is_free' => $is_free,
                'look' => $look,
                'save' => $save,
                'praise' => $praise,
                'publish_date' => $publish_date,
                'last_look_date' => $last_look_date
            ];
        }

        if ($this->fakerModel->addFakerQuestion($data)) {
            echo 'Question 数据添加成功!';
        } else {
            echo 'Question 数据添加失败!';
        }
    }

    /**
     * faker category 数据
     */
    public function faker_category()
    {
        $xml = simplexml_load_file(ROOTPATH . 'category.xml');
        $data = [];

        $index = 1;

        foreach ($xml->category as $category) {
            $data[] = [
                'id' => $index,
                'name' => (string)$category->name,
                'image' => '',
                'parent_id' => 0
            ];
            $index++;
        }

        $p = 1;
        foreach ($xml->category as $category) {
            foreach ($category->items->item as $item) {
                $data[] = [
                    'id' => $index,
                    'name' => (string)$item,
                    'image' => '',
                    'parent_id' => $p
                ];
                $index++;
            }
            $p++;
        }

        if ($this->fakerModel->addFakerCategory($data)) {
            echo 'Category 数据添加成功!';
        } else {
            echo 'Category 数据添加失败!';
        }
    }

    /**
     * faker video 数据
     */
    public function faker_video()
    {
        $msg = '';
        for ($i = 0; $i < 50; $i++) {
            $data = [];
            $id = $i + 1;
            $title = $this->faker->text(30);
            $description = implode('\n', $this->faker->paragraphs(6));
            $image = $this->faker->imageUrl(750, 350);
            $url = '';
            $category = $this->faker->numberBetween(6, 38);
            $learn = $this->faker->numberBetween(1, 10000);
            $save = $this->faker->numberBetween(1, 400);
            $last_look_date = $this->faker->unixTime('now');
            $publish_date = $this->faker->unixTime($last_look_date);

            $data[] = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'url' => $url,
                'category' => $category,
                'learn' => $learn,
                'save' => $save,
                'publish_date' => $publish_date,
                'last_look_date' => $last_look_date
            ];

            if ($this->fakerModel->addFakerVideo($data)) {
                $res = upload_file_to_qiniu(download_file_by_curl($image), 'video', 'image', $id);
                if ($res) {
                    $msg .= 'Video 添加成功!' . '<br/>';
                } else {
                    $msg .= 'Video 添加失败!' . '<br/>';
                }
            } else {
                $msg .= 'Video 添加失败!' . '<br/>';
            }
        }

        echo $msg;
    }

    /**
     * faker video comment 数据
     */
    public function faker_videoComment()
    {
        $data = [];
        for ($i = 0; $i < 200; $i++) {
            $id = $i + 1;
            $video_id = $this->faker->numberBetween(1, 50);
            $user_id = $this->faker->numberBetween(1, 30);
            $comment = $this->faker->sentence(10);
            $publish_date = $this->faker->unixTime('now');
            $praise = $this->faker->numberBetween(0, 20);
            if ($this->faker->randomElement([0, 1, 2]) === 0) {
                $sub_id = $this->faker->numberBetween(1, $i + 1);
            } else {
                $sub_id = 0;
            }

            $data[] = [
                'id' => $id,
                'video_id' => $video_id,
                'user_id' => $user_id,
                'comment' => $comment,
                'publish_date' => $publish_date,
                'praise' => $praise,
                'sub_id' => $sub_id
            ];
        }

        if ($this->fakerModel->addFakerVideoComment($data)) {
            echo 'Video Comment 数据添加成功!';
        } else {
            echo 'Video Comment 数据添加失败!';
        }
    }

    /**
     * faker resource 数据
     */
    public function faker_resource()
    {
        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $id = $i + 1;
            $title = $this->faker->text(30);
            $description = implode('\n', $this->faker->paragraphs(3));
            $resource_type = $this->faker->randomElement(['pdf', 'torrent', 'video', 'text', 'ppt', 'excel', 'code', 'sound', 'image', 'others']);
            $url = '';
            $user_id = $this->faker->numberBetween(1, 30);
            $category_id = $this->faker->numberBetween(6, 38);
            $save = $this->faker->numberBetween(1, 50);
            $look = $this->faker->numberBetween(1, 200);
            $download = $this->faker->numberBetween(1, 50);
            $last_look_date = $this->faker->unixTime('now');
            $publish_date = $this->faker->unixTime($last_look_date);

            $data[] = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'resource_type' => $resource_type,
                'url' => $url,
                'user_id' => $user_id,
                'category_id' => $category_id,
                'save' => $save,
                'look' => $look,
                'download' => $download,
                'publish_date' => $publish_date,
                'last_look_date' => $last_look_date
            ];
        }

        if ($this->fakerModel->addFakerResource($data)) {
            echo 'Resource 数据添加成功!';
        } else {
            echo 'Resource 数据添加失败!';
        }
    }

    /**
     * faker resource comment 数据
     */
    public function faker_resourceComment()
    {
        $data = [];
        for ($i = 0; $i < 200; $i++) {
            $id = $i + 1;
            $resource_id = $this->faker->numberBetween(1, 50);
            $user_id = $this->faker->numberBetween(1, 30);
            $comment = $this->faker->sentence(10);
            $publish_date = $this->faker->unixTime('now');
            $praise = $this->faker->numberBetween(0, 20);
            if ($this->faker->randomElement([0, 1, 2]) !== 0) {
                $sub_id = 0;
            } else {
                $sub_id = $this->faker->numberBetween(1, $i + 1);
            }

            $data[] = [
                'id' => $id,
                'resource_id' => $resource_id,
                'user_id' => $user_id,
                'comment' => $comment,
                'publish_date' => $publish_date,
                'praise' => $praise,
                'sub_id' => $sub_id
            ];
        }

        if ($this->fakerModel->addFakerResourceComment($data)) {
            echo 'Resource Comment 数据添加成功!';
        } else {
            echo 'Resource Comment 数据添加失败!';
        }
    }

    /**
     * faker ouser 数据   -- 企业账户等
     */
    public function faker_ouser()
    {
        $msg = '';
        for ($i = 0; $i < 20; $i++) {
            $data = [];
            $id = $i + 1;
            $username_type = $this->faker->randomElement(['email', 'phone', 'customer']);
            switch ($username_type) {
                case 'email': {
                    $username = $this->faker->safeEmail;
                    $email = $username;
                    $phone = '';
                }
                    break;
                case 'phone' : {
                    $username = $this->faker->phoneNumber;
                    $phone = $username;
                    $email = '';
                }
                    break;
                case 'customer' : {
                    $username = $this->faker->regexify('[a-zA-Z0-9]{6,10}');
                    $email = '';
                    $phone = '';
                }
                    break;
            }
            $password = md5($this->config->item('si_md5') . $username);
            $nickname = $this->faker->text(15);
            $image = $this->faker->imageUrl(120, 120);
            $is_approved = $this->faker->randomElement([0, 1]);
            $city_code = $this->faker->numberBetween(1, 216);
            $user_type = $this->faker->numberBetween(3, 4, 5);
            $last_login_city = $this->faker->numberBetween(1, 216);
            $last_login_date = $this->faker->unixTime('now');
            $last_register_date = $this->faker->unixTime($last_login_date);
            $is_active = 1;
            $active_date = $this->faker->unixTime($last_register_date);
            $apply_date = 1800;
            $apply_code = $this->faker->regexify('[0-9A-Z]{4,6}');
            $data[] = [
                'id' => $id,
                'username' => $username,
                'password' => $password,
                'nickname' => $nickname,
                'username_type' => $username_type,
                'email' => $email,
                'phone' => $phone,
                'image' => $image,
                'is_approved' => $is_approved,
                'city_code' => $city_code,
                'user_type' => $user_type,
                'last_login_city' => $last_login_city,
                'last_login_date' => $last_login_date,
                'last_register_date' => $last_register_date,
                'is_active' => $is_active,
                'active_date' => $active_date,
                'apply_date' => $apply_date,
                'apply_code' => $apply_code
            ];

            if ($this->fakerModel->addFakerOuser($data)) {
                $res = upload_file_to_qiniu(download_file_by_curl($image), 'ouser', 'image', $id);
                if ($res) {
                    $msg .= 'Ouser 添加成功!' . '<br/>';
                } else {
                    $msg .= 'Ouser 添加失败!' . '<br/>';
                }
            } else {
                $msg .= 'Ouser 添加失败!' . '<br/>';
            }
        }

        echo $msg;
    }

    /**
     * faker activity 数据
     */
    public function faker_activity()
    {
        $msg = '';
        for ($i = 0; $i < 50; $i++) {
            $data = [];
            $id = $i + 1;
            $ouser_id = $this->faker->numberBetween(1, 20);
            $title = $this->faker->text(30);
            $description = implode('\n', $this->faker->paragraphs(6));
            $address = $this->faker->address;
            $image = $this->faker->imageUrl(600, 300);
            $last_look_date = $this->faker->unixTime('now');
            $publish_date = $this->faker->unixTime($last_look_date);
            $e_date = $this->faker->unixTime('now');
            $s_date = $this->faker->unixTime($e_date);
            $allow_personal = $this->faker->randomElement([0, 1]);
            $allow_team = $this->faker->randomElement([0, 1]);
            $allow_teacher = $this->faker->randomElement([0, 1]);
            $team_min_number = $this->faker->randomElement([1, 2, 3]);
            $team_max_number = $this->faker->randomElement([4, 5, 6]);
            $save = $this->faker->numberBetween(1, 200);
            $look = $this->faker->numberBetween(1, 800);
            $join = $this->faker->numberBetween(1, 50);

            $data[] = [
                'id' => $id,
                'ouser_id' => $ouser_id,
                'title' => $title,
                'description' => $description,
                'address' => $address,
                'image' => $image,
                'publish_date' => $publish_date,
                'last_look_date' => $last_look_date,
                's_date' => $s_date,
                'e_date' => $e_date,
                'allow_personal' => $allow_personal,
                'allow_team' => $allow_team,
                'allow_teacher' => $allow_teacher,
                'team_min_number' => $team_min_number,
                'team_max_number' => $team_max_number,
                'save' => $save,
                'look' => $look,
                'join' => $join
            ];
            if ($this->fakerModel->addFakerActivity($data)) {
                $res = upload_file_to_qiniu(download_file_by_curl($image), 'activity', 'image', $id);
                if ($res) {
                    $msg .= 'Activity 添加成功!' . '<br/>';
                } else {
                    $msg .= 'Activity 添加失败!' . '<br/>';
                }
            } else {
                $msg .= 'Activity 添加失败!' . '<br/>';
            }
        }

        echo $msg;
    }

    /**
     * faker activity comment 数据
     */
    public function faker_activityComment()
    {
        $data = [];
        for ($i = 0; $i < 200; $i++) {
            $id = $i + 1;
            $activity_id = $this->faker->numberBetween(1, 50);
            $user_id = $this->faker->numberBetween(1, 30);
            $comment = $this->faker->sentence(10);
            $publish_date = $this->faker->unixTime('now');
            $praise = $this->faker->numberBetween(0, 20);
            if ($this->faker->randomElement([0, 1, 2]) !== 0) {
                $sub_id = 0;
            } else {
                $sub_id = $this->faker->numberBetween(1, $i + 1);
            }

            $data[] = [
                'id' => $id,
                'activity_id' => $activity_id,
                'user_id' => $user_id,
                'comment' => $comment,
                'publish_date' => $publish_date,
                'praise' => $praise,
                'sub_id' => $sub_id
            ];
        }

        if ($this->fakerModel->addFakerActivityComment($data)) {
            echo 'Activity Comment 数据添加成功!';
        } else {
            echo 'Activity Comment 数据添加失败!';
        }
    }

    /**
     * faker activity experience 数据
     */
    public function faker_experience()
    {
        $data = [];
        for ($i = 0; $i < 50; $i++) {
            $id = $i + 1;
            $user_id = $this->faker->numberBetween(1, 30);
            $title = $this->faker->text(30);
            $content = implode('\n', $this->faker->paragraphs(6));
            $last_look_date = $this->faker->unixTime('now');
            $publish_date = $this->faker->unixTime($last_look_date);
            $save = $this->faker->numberBetween(1, 50);
            $look = $this->faker->numberBetween(1, 200);
            $praise = $this->faker->numberBetween(1, 20);

            $data[] = [
                'id' => $id,
                'user_id' => $user_id,
                'title' => $title,
                'content' => $content,
                'publish_date' => $publish_date,
                'last_look_date' => $last_look_date,
                'save' => $save,
                'look' => $look,
                'praise' => $praise
            ];
        }

        if ($this->fakerModel->addFakerExperience($data)) {
            echo 'Experience 数据添加成功!';
        } else {
            echo 'Experience 数据添加失败!';
        }
    }

    /**
     * faker experience comment 数据
     */
    public function faker_experienceComment()
    {
        $data = [];
        for ($i = 0; $i < 200; $i++) {
            $id = $i + 1;
            $experience_id = $this->faker->numberBetween(1, 50);
            $user_id = $this->faker->numberBetween(1, 30);
            $comment = $this->faker->sentence(10);
            $publish_date = $this->faker->unixTime('now');
            $praise = $this->faker->numberBetween(0, 20);
            if ($this->faker->randomElement([0, 1, 2]) !== 0) {
                $sub_id = 0;
            } else {
                $sub_id = $this->faker->numberBetween(1, $i + 1);
            }

            $data[] = [
                'id' => $id,
                'experience_id' => $experience_id,
                'user_id' => $user_id,
                'comment' => $comment,
                'publish_date' => $publish_date,
                'praise' => $praise,
                'sub_id' => $sub_id
            ];
        }

        if ($this->fakerModel->addFakerExperienceComment($data)) {
            echo 'Experience Comment 数据添加成功!';
        } else {
            echo 'Experience Comment 数据添加失败!';
        }
    }


    /**
     * faker topic 数据
     */
    public function faker_topic() {
        $data = [];
        $topics = ['面试', '留学', '趣事', '面经', '经验', '考试', '比赛', '大学', '英雄联盟', 'BAT'];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'id' => $i + 1,
                'name' => $topics[$i],
                'dynamic_num' => 0
            ];
        }

        // 添加话题
        if ($this->fakerModel->addFakerTopic($data)) {
            echo 'Topic 数据添加成功!'.'<br/>';
        } else {
            echo 'Topic 数据添加失败!'.'<br/>';
        }
    }

    /**
     * faker topic 数据
     */
    public function faker_dynamic()
    {
        $msg = '';
        $index = 17;
        $imageData = [];
        $dataDynamic = [];
        for ($i = 6; $i < 10; $i++) { // 共10个话题
            $dynamic_num = $this->faker->numberBetween(0, 4);
            // 构造动态
            for ($j = 0; $j < $dynamic_num; $j++) {
                $id = $index;
                $user_id = $this->faker->numberBetween(1, 30);
                $has_topic = 1;
                $topic_id = $i + 1;
                $content = $this->faker->sentence(10);
                $last_look_date = $this->faker->unixTime('now');
                $publish_date = $this->faker->unixTime($last_look_date);
                $share = $this->faker->numberBetween(1, 10);
                $look = $this->faker->numberBetween(1, 100);
                $praise = $this->faker->numberBetween(1, 10);
                $dataDynamic[] = [
                    'id' => $id,
                    'user_id' => $user_id,
                    'has_topic' => $has_topic,
                    'topic_id' => $topic_id,
                    'content' => $content,
                    'publish_date' => $publish_date,
                    'last_look_date' => $last_look_date,
                    'share' => $share,
                    'look' => $look,
                    'praise' => $praise
                ];

                // 构造图片
                $image_num = $this->faker->numberBetween(0, 6);
                for ($k = 0; $k < $image_num; $k++) {
                    $imageData[] = [
                        'dynamic_id' => $id,
                        'url' => $this->faker->imageUrl(640, 480)
                    ];
                }

                $index++;
            }
        }

        // 添加动态信息
        if ($this->fakerModel->addFakerDynamic($dataDynamic)) {
            $msg .= 'Dynamic 动态添加成功!'.'<br/>';
        } else {
            $msg .= 'Dynamic 动态添加失败!'.'<br/>';
        }

        // 上传图片
        $nums = count($imageData);
        for ($l = 0; $l < $nums; $l++){
            $res = upload_file_to_qiniu(download_file_by_curl($imageData[$l]['url']), 'dynamic_image', 'url', $imageData[$l]['dynamic_id']);
            if ($res) {
                $msg .= $index.' : '.($l+1).' ------ 上传成功!'.'<br/>';
            } else {
                $msg .= $index.' : '.($l+1).' ------ 上传失败!'.'<br/>';
            }
        }


        echo $msg;
    }

    /**
     *
     */
    public function local_faker_dynamic() {
        $index = 36;
        $dataDynamic = [];
        for ($i = 0; $i < 10; $i++) { // 共10个话题
            $dynamic_num = $this->faker->numberBetween(0, 4);
            // 构造动态
            for ($j = 0; $j < $dynamic_num; $j++) {
                $id = $index;
                $user_id = $this->faker->numberBetween(1, 10);
                $has_topic = 1;
                $topic_id = $i + 1;
                $content = $this->faker->sentence(10);
                $last_look_date = $this->faker->unixTime('now');
                $publish_date = $this->faker->unixTime($last_look_date);
                $share = $this->faker->numberBetween(1, 10);
                $look = $this->faker->numberBetween(1, 100);
                $praise = $this->faker->numberBetween(1, 10);
                $dataDynamic[] = [
                    'id' => $id,
                    'user_id' => $user_id,
                    'has_topic' => $has_topic,
                    'topic_id' => $topic_id,
                    'content' => $content,
                    'publish_date' => $publish_date,
                    'last_look_date' => $last_look_date,
                    'share' => $share,
                    'look' => $look,
                    'praise' => $praise
                ];

                $index++;
            }
        }

        // 添加动态信息
        if ($this->fakerModel->addFakerDynamic($dataDynamic)) {
            echo 'Dynamic 动态添加成功!'.'<br/>';
        } else {
            echo 'Dynamic 动态添加失败!'.'<br/>';
        }
    }

    public function local_faker_dynamicImage() {
        $data = [];
        for ($i=0; $i<10; $i++) {
            $imageNum = $this->faker->numberBetween(0, 6);
            for ($j=0;$j<$imageNum;$j++) {
                $id = $i+1;
                $url = $this->faker->imageUrl(400, 600);
                $data[] = [
                    'url' => $url,
                    'dynamic_id' => $id
                ];
            }
        }

        if ($this->fakerModel->addFakerDynamicImages($data)) {
            echo 'Dynamic Images 添加成功!';
        } else {
            echo 'Dynamic Images 添加失败!';
        }
    }

    /**
     * faker dynamic comment 数据
     */
    public function faker_dynamicComment()
    {
        $data = [];
        for ($i = 0; $i < 300; $i++) {
            $id = $i + 1;
            $dynamic_id = $this->faker->numberBetween(1, 20);
            $user_id = $this->faker->numberBetween(1, 30);
            $comment = $this->faker->sentence(10);
            $publish_date = $this->faker->unixTime('now');
            $praise = $this->faker->numberBetween(0, 20);
            if ($this->faker->randomElement([0, 1, 2]) !== 0) {
                $sub_id = 0;
            } else {
                $sub_id = $this->faker->numberBetween(1, $i + 1);
            }

            $data[] = [
                'id' => $id,
                'dynamic_id' => $dynamic_id,
                'user_id' => $user_id,
                'comment' => $comment,
                'publish_date' => $publish_date,
                'praise' => $praise,
                'sub_id' => $sub_id
            ];
        }

        if ($this->fakerModel->addFakerDynamicComment($data)) {
            echo 'Dynamic Comment 数据添加成功!';
        } else {
            echo 'Dynamic Comment 数据添加失败!';
        }
    }

    /**
     * faker news type 数据
     */
    public function faker_newsType()
    {
        $data = [
            [
                'id' => 1,
                'name' => '数学建模',
                'image' => ''
            ],
            [
                'id' => 2,
                'name' => '奥数竞赛',
                'image' => ''
            ],
            [
                'id' => 3,
                'name' => '微软竞赛',
                'image' => ''
            ],
            [
                'id' => 4,
                'name' => '小型比赛',
                'image' => ''
            ],
            [
                'id' => 5,
                'name' => 'BAT',
                'image' => ''
            ],
            [
                'id' => 6,
                'name' => '搜狐网',
                'image' => ''
            ],
            [
                'id' => 7,
                'name' => '新浪网',
                'image' => ''
            ],
            [
                'id' => 8,
                'name' => '网易网',
                'image' => ''
            ],
            [
                'id' => 9,
                'name' => '校园网',
                'image' => ''
            ],
            [
                'id' => 10,
                'name' => '国家级',
                'image' => ''
            ]
        ];

        if ($this->fakerModel->addFakerNewsType($data)) {
            echo 'News Type 添加成功!';
        } else {
            echo 'News Type 添加失败!';
        }
    }

    /**
     * faker news 数据
     */
    public function faker_news()
    {
        $msg = '';
        for ($i = 0; $i < 50; $i++) {
            $data = [];
            $id = $i + 1;
            $title = $this->faker->text(30);
            $description = implode('\n', $this->faker->paragraphs(6));
            $image = $this->faker->imageUrl(750, 350);
            $news_type = $this->faker->numberBetween(1, 10);
            $last_look_date = $this->faker->unixTime('now');
            $publish_date = $this->faker->unixTime($last_look_date);
            $e_date = $this->faker->unixTime('now');
            $s_date = $this->faker->unixTime($e_date);
            $allow_personal = $this->faker->randomElement([0, 1]);
            $allow_team = $this->faker->randomElement([0, 1]);
            $allow_teacher = $this->faker->randomElement([0, 1]);
            $team_min_number = $this->faker->randomElement([1, 2, 3]);
            $team_max_number = $this->faker->randomElement([4, 5, 6]);
            $save = $this->faker->numberBetween(1, 20);
            $look = $this->faker->numberBetween(1, 100);
            $join = $this->faker->numberBetween(1, 10);

            $data[] = [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'news_type' => $news_type,
                'publish_date' => $publish_date,
                'last_look_date' => $last_look_date,
                's_date' => $s_date,
                'e_date' => $e_date,
                'allow_personal' => $allow_personal,
                'allow_team' => $allow_team,
                'allow_teacher' => $allow_teacher,
                'team_min_number' => $team_min_number,
                'team_max_number' => $team_max_number,
                'save' => $save,
                'look' => $look,
                'join' => $join
            ];

            if ($this->fakerModel->addFakerNews($data)) {
                $res = upload_file_to_qiniu(download_file_by_curl($image), 'news', 'image', $id);
                if ($res) {
                    $msg .= 'News 数据添加成功!' . '<br/>';
                } else {
                    $msg .= 'News 数据添加失败!' . '<br/>';
                }

            } else {
                $msg .= 'News 数据添加失败!' . '<br/>';
            }
        }
        echo $msg;
    }

    /**
     * faker news comment 数据
     */
    public function faker_newsComment()
    {
        $data = [];
        for ($i = 0; $i < 200; $i++) {
            $id = $i + 1;
            $news_id = $this->faker->numberBetween(1, 50);
            $user_id = $this->faker->numberBetween(1, 30);
            $comment = $this->faker->sentence(10);
            $publish_date = $this->faker->unixTime('now');
            $praise = $this->faker->numberBetween(0, 20);
            if ($this->faker->randomElement([0, 1, 2]) !== 0) {
                $sub_id = 0;
            } else {
                $sub_id = $this->faker->numberBetween(1, $i + 1);
            }

            $data[] = [
                'id' => $id,
                'news_id' => $news_id,
                'user_id' => $user_id,
                'comment' => $comment,
                'publish_date' => $publish_date,
                'praise' => $praise,
                'sub_id' => $sub_id
            ];
        }

        if ($this->fakerModel->addFakerNewsComment($data)) {
            echo 'News Comment 数据添加成功!';
        } else {
            echo 'News Comment 数据添加失败!';
        }
    }

    /**
     * faker follow 数据
     */
    public function faker_follow()
    {
        $data = [];
        for ($i = 0; $i < 300; $i++) {
            $id = $i + 1;
            $user_id = $this->faker->numberBetween(1, 30);
            $follow_user_id = $this->faker->numberBetween(1, 30);
            if ($user_id === $follow_user_id) {
                $follow_user_id = $this->faker->numberBetween(1, 30);
            }

            $data[] = [
                'id' => $id,
                'user_id' => $user_id,
                'follow_user_id' => $follow_user_id
            ];
        }

        if ($this->fakerModel->addFakerFollow($data)) {
            echo 'Follow 数据添加成功!';
        } else {
            echo 'Follow 数据添加失败!';
        }
    }

    /**
     * faker friend 数据
     */
    public function faker_friend()
    {
        $data = [];
        for ($i = 0; $i < 300; $i++) {
            $id = $i + 1;
            $user_id = $this->faker->numberBetween(1, 30);
            $friend_user_id = $this->faker->numberBetween(1, 30);
            if ($user_id === $friend_user_id) {
                $friend_user_id = $this->faker->numberBetween(1, 30);
            }

            $data[] = [
                'id' => $id,
                'user_id' => $user_id,
                'friend_user_id' => $friend_user_id
            ];
        }

        if ($this->fakerModel->addFakerFriend($data)) {
            echo 'Friend 数据添加成功!';
        } else {
            echo 'Friend 数据添加失败!';
        }
    }

    /**
     *  faker team
     */
    public function faker_team() {
        for ($i =0; $i < 50; $i ++) {
            $id = $i+1;
            $name = $this->faker->text(10);
            $member_num = $this->faker->numberBetween(2, 5); // 不包括老师
            $teacher_num = $this->faker->randomElement([0, 1]); // 有或者没有

            // 添加team 返回id
            $team_id = $this->fakerModel->addTeam([
                'id' => $id,
                'name' => $name,
                'member_num' => $member_num,
                'teacher_num' => $teacher_num
            ]);

            $members = [];
            for ($k = 0; $k < $member_num; $k++) {
                // 添加 team member数据
                $user_id = $this->faker->randomElement([2, 3, 5, 10, 13, 19, 22, 28, 30, 31]);
                if ($k === 0) {
                    $is_leader = 1;
                } else {
                    $is_leader = 0;
                }
                $is_teacher = 0;
                $members[] = [
                    'team_id' => $team_id,
                    'user_id' => $user_id,
                    'is_leader' => $is_leader,
                    'is_teacher' => $is_teacher
                ];
            }

            // 添加成员
            $this->fakerModel->addTeamMemebr($members);

            if ($teacher_num) {
                // 添加老师
                $user_id = $this->faker->randomElement([1, 4, 9, 11, 12, 14, 16, 17, 21]);
                $is_leader = 0;
                $is_teacher = 1;
                $teacher = [
                    'team_id' => $team_id,
                    'user_id' => $user_id,
                    'is_leader' => $is_leader,
                    'is_teacher' => $is_teacher
                ];

                $this->fakerModel->addTeacher($teacher);
            }
        }
    }

    /**
     * faker team member
     */
    public function faker_message() {
        $messages = [];
        for ($i = 0; $i < 1000; $i++ ) {
            $message_type = $this->faker->randomElement([0, 1, 2]);
            if ($message_type === 0) {
                // 纯文本
                $message = $this->faker->text(100);
            }

            if ($message_type === 1) {
                // 图片
                $message = $this->faker->randomElement([
                    'http://oaetkzt9k.bkt.clouddn.com/FgCcdKBAPX0OepScvPLVyxI73_j6',
                    'http://oaetkzt9k.bkt.clouddn.com/FgEqGtuzqWJ6niopbRv7mQsNpS4K',
                    'http://oaetkzt9k.bkt.clouddn.com/FgoibRmg-TFskDryL3G18fX2pYJr',
                    'http://oaetkzt9k.bkt.clouddn.com/FhMDBGWMGG7yGxGhGbzfYSNnqsi7',
                    'http://oaetkzt9k.bkt.clouddn.com/C.jpg',
                    'http://oaetkzt9k.bkt.clouddn.com/C++.jpg'
                ]);
            }

            if ($message_type === 2) {
                // 音频
                $message = $this->faker->randomElement([
                    'http://oaetkzt9k.bkt.clouddn.com/sendmsg.caf',
                    'http://oaetkzt9k.bkt.clouddn.com/msg.caf'
                ]);
            }

            $from = $this->faker->numberBetween(10, 30);
            $to = $this->faker->numberBetween(1, 30);
            $team_id = 0;
            $date = $this->faker->unixTime('now');
            $messages[] = [
                'message' => $message,
                'message_type' => $message_type,
                'from' => $from,
                'to' => $to,
                'team_id' => $team_id,
                'date' => $date
            ];
        }

        // 添加消息
        $this->fakerModel->addMessage($messages);
    }

}