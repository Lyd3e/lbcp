<?php
/**
 * LYD3E验证类库
 *
 * @author Raj Luo
 */

namespace Lyd3e\Lbcp\Safety;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Lyd3e\Lbcp\Guidance\ErrorCode;

class Lyd3e extends Controller
{
    /**
     * 参数容器
     *
     * @var string
     */
    public $params = '';

    /**
     * Lyd3e constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->paramsToDecrypt($request);
    }

    /**
     * 参数解密
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function paramsToDecrypt($request)
    {
        //兼容json/urlencode
        $request = $request->all();

        //验证请求参数
        $validator = Validator::make($request, [
            'params' => 'required'
        ]);

        //获取参数错误的信息
        if ($validator->fails()) {
            return $this->responseHandler('A0410', $validator->errors()->first());
        }

        //解密请求参数
        $params = $request['params'];
        
        try {
            $params = Crypt::decryptString($params);

        } catch (\Exception $e) {
            return $this->responseHandler('A0427');
        }

        //保存解密后的参数
        $this->params = json_decode($params, true);
    }

    /**
     * 响应处理程序
     *
     * @param string $code
     * @param string $message
     * @param string $data
     * @param string $httpCode
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function responseHandler($code='00000', $message='', $data='NO_DATA_TYPE', $httpCode='200')
    {
        //错误信息处理
        if (!isset($message) || empty($message)) {
            $message = ErrorCode::getErrorMessage($code);
        }

        $return = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ];

        //空数据处理
        if (!isset($data) || empty($data)) {
            $data = [];
        }
		
		//无数据类型处理
        if ($data == 'NO_DATA_TYPE') {
            unset($return['data']);
        }

        /*//错误响应日志记录
        if ($code != '00000') {

        }*/
		
        return response()->json($return)->setEncodingOptions(JSON_UNESCAPED_UNICODE)->setStatusCode($httpCode);
    }

    /**
     * 参数加密
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\JsonResponse
     */
    /*public function paramsToEncrypt()
    {
        //file_get_contents("php://input")获取请求原始数据流
        $params = json_decode(file_get_contents("php://input"));

        return $this->responseHandler('00000', null, Crypt::encryptString(json_encode($params)));
    }*/
}