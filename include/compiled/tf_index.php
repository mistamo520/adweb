<?php include template("index_header");?>
<?php include template("tf_left_menu");?>
  <div class="layui-body">
    <!-- 内容主体区域 -->
	<div class="layuibodycont">
		<div class="topblock clearfix">
			<dl class="topitemdl topitemdl30">
				<dd>昨日预计收益</dd>
				<dt>￥93000.00</dt>
			</dl>
			<dl class="topitemdl topitemdl30">
				<dd>总收益金额</dd>
				<dt>￥23040.30</dt>
			</dl>
			<dl class="topitemdl topitemdl30 noborder">
				<dd>今日消耗</dd>
				<dt>￥23040.30</dt>
			</dl>
		</div>
		<div class="clearfix navtabs">
			<span class="navpan active">近7天</span>
			<span class="navpan">本月</span>
			<div class="layui-inline" style="margin-top:4px;">
			  <label class="layui-form-label">时间</label>
			  <div class="layui-input-inline">
				<input type="text" class="layui-input" id="test6" placeholder="开始 到 结束">
			  </div>
			</div>
		</div>
		<div class="clearfix">
			<div class="countitem fl">
				<div class="countnav"><p class="fl countleft"><i><img src="/static/images/icon04.png"></i><span>广告推广量</span></p>
				</div>
				<div class="">
					<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
				<div id="report-chart" class="report-chart" style="height:400px" data-action="week"></div>
				<!-- ECharts单文件引入 -->

				</div>
			</div>
			<div class="countitem fr">
				<div class="countnav"><p class="fl countleft"><i><img src="/static/images/icon02.png"></i><span>收益</span></p></div>
				<div class="">
					<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
					<div id="report-chart2" class="report-chart" style="height:400px" data-action="week"></div>
				<!-- ECharts单文件引入 -->
				</div>
			</div>
		</div>
    </div>
  </div>
  <!--<div class="site-tree-mobile layui-hide">
	  <i class="layui-icon layui-icon01"></i>
  </div>-->

</div>
<script type="text/javascript" src="/static/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="/static/js/global.js"></script>
<script type="text/javascript" src="/static/js/moment.min.js"></script>
<script type="text/javascript" src="/static/js/detect-report.js"></script>
<script type="text/javascript" src="/static/js/echarts.common.min.js"></script>
<script>
layui.use('laydate', function(){
  var laydate = layui.laydate;
//日期范围
  laydate.render({
    elem: '#test6'
    ,range: true
  });
 });
// 基于准备好的dom，初始化echarts实例
var myChart = echarts.init(document.getElementById('report-chart'));
var myChart2 = echarts.init(document.getElementById('report-chart2'));
// 指定图表的配置项和数据
var option = {
    title : {
       // text: '某楼盘销售情况',
       // subtext: '纯属虚构'
    },
    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['展示','点击']
    },
    toolbox: {
        feature: {
           // saveAsImage: {}//将统计图保存为
        }
        ,right:100
        ,top:0
    },
	grid: {
        left: '3%',
        right: '4%',
        bottom: '9%',
        containLabel: true
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : ['6.21','6.22','6.23','6.24','6.25','6.26','6.27']
        }
    ],
    yAxis: {
		'name':'(个)',
        type: 'value'
    },
    series : [
        {
            name:'展示',
            type:'line',
           // smooth:true,
            itemStyle: {normal: {areaStyle: {type: 'default'},label : {
								show:true,
								position:'top',
								formatter:'{c}'
							},
							areaStyle:{
								color:new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ 
									offset: 0,
									color: '#afeff3'
								}, {
									offset: .34,
									color: '#dcf8fa'
								},{
									offset: 1,
									color: '#fff'
								}])
							},
							color:'#2cc6ad'}},
            data:[10, 12, 21, 54, 260, 830, 710]
        },
        {
            name:'点击',
            type:'line',
           // smooth:true,
			itemStyle: {normal: {
			areaStyle: {type: 'default'},
			label : {show:true,position:'top',formatter:'{c}'},
			areaStyle:{
								color:new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ 
									offset: 0,
									color: '#ffd280'
								}, {
									offset: .34,
									color: '#ffe7ba'
								},{
									offset: 1,
									color: '#fff'
								}])
							},color:'#ffc400'
			}},
            data:[30, 182, 434, 791, 390, 30, 10]
        }
    ]
};
// 使用刚指定的配置项和数据显示图表。
myChart.setOption(option);

var option2 = {
    title : {
       // text: '某楼盘销售情况',
       // subtext: '纯属虚构'
    },
    tooltip : {
        trigger: 'axis'
    },
    legend: {
        data:['收益']
    },
    toolbox: {
        feature: {
           // saveAsImage: {}//将统计图保存为
        }
        ,right:100
        ,top:0
    },
	grid: {
        left: '2%',
        right: '3%',
        bottom: '9%',
        containLabel: true
    },
    calculable : true,
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : ['6.21','6.22','6.23','6.24','6.25','6.26','6.27']
        }
    ],
    yAxis: {
		'name':'(元)',
        type: 'value'
    },
    series : [
        {
            name:'收益',
            type:'line',
            itemStyle: {
					normal: {
						color: '#27B6C7',
						lineStyle: {
							shadowColor : 'rgba(0,0,0,0.4)'
						}
					}
				},
            stack: '总量',
            data:[120, 132, 201, 134, 190, 230, 210]

        }
    ]
}; 
// 使用刚指定的配置项和数据显示图表。
myChart2.setOption(option2); 


window.addEventListener("resize", function () {
    myChart.resize();
	myChart2.resize();
});  
</script>
</body>
</html>
