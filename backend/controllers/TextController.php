<?php

namespace app\modules\system\controllers;

use app\components\ApiBaseController;
use app\modules\common\services\JavaInterfaceService;
use app\modules\common\services\VersionService;
use app\modules\system\services\OfficialsService;
use app\components\MyPagination;
use app\modules\user\models\User;
use yii\web\UploadedFile;

/**
 * Class description
 * Created by PhpStorm
 * User: Quanjiaxin
 * Date: 2018/12/25
 * Time: 13:37
 */
class NoticeController extends ApiBaseController
{
    /**
     * @SWG\Get(
     *   path="/system/notice/sms-template-list", tags={"Notice"},
     *   summary="短信模板列表",
     *   @SWG\Parameter(name="page", type="integer", required=false, in="query", description="页数"),
     *   @SWG\Parameter(name="limit", type="integer", required=false, in="query", description="每页显示个数"),
     *   @SWG\Parameter(name="content", type="string", required=false, in="query", description="短信内容"),
     *   @SWG\Parameter(name="category", type="string", required=false, in="query", description="短信类型"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionSmsTemplateList()
    {
        $page = $this->request->get('page', 1);
        $limit = $this->request->get('limit', 20);
        $content = $this->request->get('content', '');
        $category = $this->request->get('category', '');

        $response = SmsDayuService::getInstance()->getTemplateList($page, $limit, $content, $category);
        $pages = new MyPagination(['totalCount' => (int)$response['count']]);
        $pages->pageSize = $limit;

        return $this->success([
            'list' => $response['list'],
            'pages' => $pages,
            'categoryList' => SmsDayuService::$templateCategory,
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/system/notice/delete-sms-template", tags={"Notice"},
     *   summary="删除短信模板",
     *   @SWG\Parameter(name="templateId", type="string", required=true, in="query", description="模板ID"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionDeleteSmsTemplate()
    {
        $templateId = $this->request->post('templateId', 0);
        if (!$templateId) {
            return $this->error('请选择需要删除的模板');
        }

        $response = SmsDayuService::getInstance()->deleteTemplate($templateId);
        if ($response['status'] == 0) {
            return $this->success();
        }

        return $this->error($response['message']);
    }

    /**
     * @SWG\Post(
     *   path="/system/notice/change-template-status", tags={"Notice"},
     *   summary="修改短信模板状态",
     *   @SWG\Parameter(name="templateId", type="string", required=true, in="query", description="模板ID"),
     *   @SWG\Parameter(name="status", type="integer", required=true, in="query", description="修改状态 0：弃用；1：正常"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionChangeTemplateStatus()
    {
        $templateId = $this->request->post('templateId', 0);
        if (!$templateId) {
            return $this->error('请选择需要操作的模板');
        }

        $status = $this->request->post('status', false);
        if (false === $status) {
            return $this->error('请选择需要操作的状态');
        }

        $response = SmsDayuService::getInstance()->changeTemplateStatus($templateId, $status);
        if ($response['status'] == 0) {
            return $this->success();
        }

        return $this->error($response['message']);
    }

    /**
     * @SWG\Get(
     *   path="/system/notice/sms-template-detail", tags={"Notice"},
     *   summary="获取短信模板详情",
     *   @SWG\Parameter(name="templateId", type="string", required=true, in="query", description="模板ID"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionSmsTemplateDetail()
    {
        $templateId = $this->request->get('templateId', 0);
        if (!$templateId) {
            return $this->error('模板ID参数错误');
        }

        $response = SmsDayuService::getInstance()->templateDetail($templateId);
        $result = [];
        if ($response['status'] == 0) {
            $result = $response['data'];
        }

        return $this->success([
            'detail' => $result,
            'categoryList' => SmsDayuService::$templateCategory
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/system/notice/edit-sms-template", tags={"Notice"},
     *   summary="编辑/添加短信模板",
     *   @SWG\Parameter(name="templateId", type="string", required=false, in="query", description="模板ID"),
     *   @SWG\Parameter(name="content", type="string", required=true, in="query", description="模板内容"),
     *   @SWG\Parameter(name="aliDomesticCode", type="string", required=false, in="query", description="阿里国内"),
     *   @SWG\Parameter(name="aliInternationalCode", type="string", required=false, in="query", description="阿里国际"),
     *   @SWG\Parameter(name="hupuCode", type="string", required=false, in="query", description="虎扑"),
     *   @SWG\Parameter(name="mwCode", type="string", required=false, in="query", description="梦网"),
     *   @SWG\Parameter(name="comment", type="string", required=false, in="query", description="备注"),
     *   @SWG\Parameter(name="category", type="integer", required=true, in="query", description="模板类型"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionEditSmsTemplate()
    {
        $templteId = $this->request->post('templateId', 0);
        $content = $this->request->post('content', '');
        $category = $this->request->post('category', '');
        $aliDomesticCode = $this->request->post('aliDomesticCode', '');
        $aliInternationalCode = $this->request->post('aliInternationalCode', '');
        $hupuCode = $this->request->post('hupuCode', '');
        $mwCode = $this->request->post('mwCode', '');
        $comment = $this->request->post('comment', '');

        if (empty($content)) {
            return $this->error('请填写短信模板内容');
        }

        if (!in_array($category, array_keys(SmsDayuService::$templateCategory))) {
            return $this->error('请选择正确的类别');
        }

        $response = SmsDayuService::getInstance()->editTemplate($content, $aliDomesticCode, $aliInternationalCode, $hupuCode, $mwCode, $comment, $category, $templteId);
        if ($response['status'] == 0) {
            return $this->success();
        }

        return $this->error($response['message']);
    }

    /**
     * @SWG\Get(
     *   path="/system/notice/list", tags={"Notice"},
     *   summary="系统通知列表",
     *   @SWG\Parameter(name="page", type="integer", required=false, in="query", description="页数"),
     *   @SWG\Parameter(name="limit", type="integer", required=false, in="query", description="每页显示个数"),
     *   @SWG\Parameter(name="status", type="integer", required=true, in="query", description="1：正常；0：已取消"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionList()
    {
        $status = (int)$this->request->get('status', 0);
        $page = (int)$this->request->get('page', 1);
        $limit = (int)$this->request->get('limit', 20);

        $response = OfficialsService::officialsList($page, $limit, $status);
        $pages = new MyPagination(['totalCount' => (int)$response['count']]);
        $pages->pageSize = $limit;

        $list = [];
        if ($response['list']) {
            $userIds = array_column($response['list'], 'userId');
            $userInfo = User::find()->select(['userId', 'userName'])->where(['userId' => $userIds])->indexBy('userId')->asArray()->all();
            $typeName = OfficialsService::$noticeType;
            $sendName = OfficialsService::$sendStatus;
            foreach ($response['list'] as $item) {
                $item['custom'] = $item['custom'] ?? [];
                $item['typeName'] = $typeName[$item['type']] ?? '未知';
                $item['userName'] = isset($userInfo[$item['userId']]) ? $userInfo[$item['userId']]['userName'] : '未知';
                $item['sendTime'] = $item['sendTime'] ? date('Y-m-d H:i:s', $item['sendTime']) : '及时发送';
                $item['sendName'] = $sendName[$item['status']] ?? '异常';
                unset($item['custom']);
                $list[] = $item;
            }
        }

        return $this->success([
            'list' => $list,
            'pages' => $pages
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/system/notice/cancel", tags={"Notice"},
     *   summary="取消系统消息",
     *   @SWG\Parameter(name="id", type="string", required=true, in="query", description="通知ID"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionCancel()
    {
        $id = $this->request->post('id');
        if (!$id) {
            return $this->error('请选择需要取消的系统消息');
        }

        $response = OfficialsService::cancel($id);

        if ($response['status'] == 0) {
            return $this->success();
        }

        return $this->error($response['message']);
    }

    /**
     * @SWG\Get(
     *   path="/system/notice/detail", tags={"Notice"},
     *   summary="系统通知详情",
     *   @SWG\Parameter(name="id", type="string", required=false, in="query", description="通知ID"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionDetail()
    {
        $id = (int)$this->request->get('id', 0);
        $detail = OfficialsService::officalDetail($id);
        $platformList = OfficialsService::$platformList;
        unset($platformList[OfficialsService::PLATFORM_USERSCSV]);

        if ($detail) {
            $detail['sendTime'] = $detail['sendTime'] ? date('Y-m-d H:i:s', $detail['sendTime']) : '';
            $detail['custom'] = $detail['custom'] ?? [];
            unset($detail['custom']);
        }

        return $this->success([
            'detail' => $detail,
            'typeList' => OfficialsService::$noticeType,
            'platformList' => $platformList,
            'ios' => VersionService::getAll(IPHONE),
            'android' => VersionService::getAll(ANDROID)
        ]);
    }

    /**
     * @SWG\Post(
     *   path="/system/notice/check-file", tags={"Notice"},
     *   summary="验证文件",
     *   @SWG\Parameter(name="file", type="string", required=true, in="query", description="文件名"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionCheckFile()
    {
        $filePath = $this->request->post('file', '');
        if (!$filePath) {
            return $this->error('请上选择需要验证的文件');
        }

        $response = OfficialsService::uploadFile($filePath);

        if ($response['status'] == 0) {
            return $this->success($response['data']);
        }

        return $this->error($response['message']);
    }

    /**
     * @SWG\Post(
     *   path="/system/notice/edit", tags={"Notice"},
     *   summary="编辑/添加通知",
     *   @SWG\Parameter(name="id", type="string", required=false, in="query", description="通知ID：不存在=添加；存在=修改 "),
     *   @SWG\Parameter(name="content", type="string", required=true, in="query", description="通知内容"),
     *   @SWG\Parameter(name="cover", type="string", required=true, in="query", description="封面图地址"),
     *   @SWG\Parameter(name="type", type="integer", required=true, in="query", description="通知类型"),
     *   @SWG\Parameter(name="unionId", type="integer", required=false, in="query", description="通知类型对应的ID"),
     *   @SWG\Parameter(name="platform", type="string", required=true, in="query", description="推送对象"),
     *   @SWG\Parameter(name="sendTime", type="string", required=false, in="query", description="发送时间  为空代表立即发送"),
     *   @SWG\Parameter(name="platformContext", type="string", required=true, in="query", description="推送对象内容，Android/Ios版本号数组、userIds、文件路径"),
     *   @SWG\Response(
     *     response=200,
     *     ref="$/responses/Json",
     *   )
     * )
     */
    public function actionEdit()
    {
        //参数验证
        $data = $this->request->post();
        if (true !== $res = $this->checkParams($data)) {
            return $this->error($res);
        }

        $data['id'] = empty($data['id']) ? '' : (int)$data['id'];
        $response = OfficialsService::saveOffical(
            $data['content'],
            $data['cover'],
            $data['type'],
            $data['unionId'],
            $data['platform'],
            $data['platformContext'],
            $this->getUserId(),
            $data['sendTime'],
            $data['id']
        );

        if ($response['status'] == 0) {
            return $this->success();
        }

        return $this->error($response['message']);
    }

    public function checkParams(&$data)
    {
        $notEmpty = [
            'content' => '通知内容',
            'cover' => '封面图',
            'unionId' => '通知类型关联ID',
            'platform' => '接收对象',
            'sendTime' => '发送时间'
        ];
        foreach ($notEmpty as $key => $value) {
            if (empty($data[$key])) {
                return "请输入或填写{$value}";
            }
        }

        if (!isset($data['platformContext'])) {
            return '接收内容参数错误';
        }

        if (!isset($data['type']) || !in_array($data['type'], array_keys(OfficialsService::$noticeType))) {
            return '请选择正确的通知类型';
        }

        switch ($data['platform']) {
            case OfficialsService::PLATFORM_ALL :
                $data['platformContext'] = '';
                break;
            case OfficialsService::PLATFORM_ANDROID :
                $data['platformContext'] = implode(',', $data['platformContext']);
                break;
            case OfficialsService::PLATFORM_IOS :
                $data['platformContext'] = implode(',', $data['platformContext']);
                break;
            default :
                break;
        }

        if (!in_array($data['platform'], array_keys(OfficialsService::$platformList))) {
            return '请选择正确的接收对象';
        }

        if (false == strtotime($data['sendTime'])) {
            return '请填写正确的发送时间格式';
        }

        return true;
    }
}