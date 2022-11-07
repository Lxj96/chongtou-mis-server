<?php
/**
 * Description: 接口文档公共注释定义
 * File: ApidocDefinitions.php
 * User: Lxj
 * DateTime: 2021-08-17 10:55
 */

namespace app\common\controller;

use hg\apidoc\annotation as Apidoc;

class ApidocDefinitions
{
    /**
     * 分页请求参数
     * @Apidoc\Param("page", type="int", default="1", desc="分页第几页")
     * @Apidoc\Param("limit", type="int", default="10", desc="分页每页数量")
     */
    public function pagingParam()
    {
    }

    /**
     * 分页返回参数
     * @Apidoc\Returned("count", type="int", default="0", desc="总数量")
     * @Apidoc\Returned("pages", type="int", default="0", desc="总页数")
     * @Apidoc\Returned("page", type="int", default="1", desc="分页第几页")
     * @Apidoc\Returned("limit", type="int", default="10", desc="分页每页数量")
     */
    public function pagingReturn()
    {
    }

    /**
     * 排序请求参数
     * @Apidoc\Param("sort_field", type="string", default="", desc="排序字段，eg：id")
     * @Apidoc\Param("sort_value", type="string", default="", desc="排序类型：desc降序、asc升序")
     */
    public function sortParam()
    {
    }

    /**
     * 搜索请求参数
     * @Apidoc\Param("search_field", type="string", default="", desc="搜索字段，eg：name")
     * @Apidoc\Param("search_value", type="string", default="", desc="搜索内容，eg：张三")
     */
    public function searchParam()
    {
    }

    /**
     * 日期请求参数
     * @Apidoc\Param("date_field", type="string", default="", desc="日期字段，eg：create_time")
     * @Apidoc\Param("date_value", type="array", default="", desc="日期范围，eg：['2020-02-02','2020-02-22']")
     */
    public function dateParam()
    {
    }

    /**
     * 验证码请求参数
     * @Apidoc\Param("captcha_id", type="string", default="", desc="验证码id")
     * @Apidoc\Param("captcha_code", type="string", default="", desc="验证码内容")
     */
    public function captchaParam()
    {
    }

    /**
     * 验证码返回参数
     * @Apidoc\Returned("captcha_switch", type="bool", default="", desc="验证码是否开启")
     * @Apidoc\Returned("captcha_id", type="string", default="", desc="验证码id")
     * @Apidoc\Returned("captcha_src", type="string", default="", desc="验证码图片")
     */
    public function captchaReturn()
    {
    }

    /**
     * 上传文件请求参数
     * @Apidoc\Param("file", type="file", require=true, default="", desc="文件")
     */
    public function fileParam()
    {
    }

    /**
     * 上传文件返回参数
     * @Apidoc\Returned("file_id", type="int", default="", desc="文件ID")
     * @Apidoc\Returned("file_name", type="string", default="", desc="文件名称")
     * @Apidoc\Returned("file_path", type="string", default="", desc="文件路径")
     * @Apidoc\Returned("file_size", type="string", default="", desc="文件大小")
     * @Apidoc\Returned("file_url", type="string", default="", desc="文件链接")
     */
    public function fileReturn()
    {
    }

    /**
     * ids请求参数
     * @Apidoc\Param("ids", type="array", require=true, default="", desc="id数组，eg：[1,2,3]")
     */
    public function idsParam()
    {
    }

    /**
     * imgs请求参数
     * @Apidoc\Param("imgs", type="array", require=false, default="[]", desc="图片",
     *     @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *     @Apidoc\Param("file_name", type="string", require=true, default="", desc="图片名称"),
     *     @Apidoc\Param("file_size", type="string", require=true, default="", desc="图片大小"),
     *     @Apidoc\Param("file_path", type="string", require=true, default="", desc="图片路径"),
     *     @Apidoc\Param("file_url", type="string", require=true, default="", desc="图片链接")
     * )
     */
    public function imgsParam()
    {
    }

    /**
     * imgs返回参数
     * @Apidoc\Returned("imgs", type="array", require=false, default="[]", desc="图片",
     *     @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *     @Apidoc\Returned("file_name", type="string", require=true, default="", desc="图片名称"),
     *     @Apidoc\Returned("file_size", type="string", require=true, default="", desc="图片大小"),
     *     @Apidoc\Returned("file_path", type="string", require=true, default="", desc="图片路径"),
     *     @Apidoc\Returned("file_url", type="string", require=true, default="", desc="图片链接")
     * )
     */
    public function imgsReturn()
    {
    }

    /**
     * files请求参数
     * @Apidoc\Param("files", type="array", require=false, default="[]", desc="附件",
     *     @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *     @Apidoc\Param("file_name", type="string", require=true, default="", desc="附件名称"),
     *     @Apidoc\Param("file_size", type="string", require=true, default="", desc="附件大小"),
     *     @Apidoc\Param("file_path", type="string", require=true, default="", desc="附件路径"),
     *     @Apidoc\Param("file_url", type="string", require=true, default="", desc="附件链接"),
     * )
     */
    public function filesParam()
    {
    }

    /**
     * files返回参数
     * @Apidoc\Returned("files", type="array", require=false, default="[]", desc="附件",
     *     @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *     @Apidoc\Returned("file_name", type="string", require=true, default="", desc="附件名称"),
     *     @Apidoc\Returned("file_size", type="string", require=true, default="", desc="附件大小"),
     *     @Apidoc\Returned("file_path", type="string", require=true, default="", desc="附件路径"),
     *     @Apidoc\Returned("file_url", type="string", require=true, default="", desc="附件链接"),
     * )
     */
    public function filesReturn()
    {
    }

    /**
     * videos请求参数
     * @Apidoc\Param("videos", type="array", require=false, default="[]", desc="视频",
     *     @Apidoc\Param("file_id", type="int", require=true, default="", desc="文件ID"),
     *     @Apidoc\Param("file_name", type="string", require=true, default="", desc="视频名称"),
     *     @Apidoc\Param("file_size", type="string", require=true, default="", desc="视频大小"),
     *     @Apidoc\Param("file_path", type="string", require=true, default="", desc="视频路径"),
     *     @Apidoc\Param("file_url", type="string", require=true, default="", desc="视频链接"),
     * )
     */
    public function videosParam()
    {
    }

    /**
     * videos返回参数
     * @Apidoc\Returned("videos", type="array", require=false, default="[]", desc="视频",
     *     @Apidoc\Returned("file_id", type="int", require=true, default="", desc="文件ID"),
     *     @Apidoc\Returned("file_name", type="string", require=true, default="", desc="视频名称"),
     *     @Apidoc\Returned("file_size", type="string", require=true, default="", desc="视频大小"),
     *     @Apidoc\Returned("file_path", type="string", require=true, default="", desc="视频路径"),
     *     @Apidoc\Returned("file_url", type="string", require=true, default="", desc="视频链接"),
     * )
     */
    public function videosReturn()
    {
    }
}
