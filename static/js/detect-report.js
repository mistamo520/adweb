
/*
 * echarts-calendar-week-month-based
 * baoshan
 * 2987900397@qq.com
 * based on jQuery and moment
 * 2016-10-21
*/
; (function($, window, document, undefined) {
    //定义Calendar的构造函数
    var Calendar = function(ele, opt) {
        this.$element = ele;

        this.defaults = {
            "calendarContent": this.$element.find('.report-chart'),
            "clickDay": this.clickDay,
			"yesterday":moment().subtract(1, 'days').calendar(),
            "today": moment(),
            "tWeekIndex": 0,
            "tMonth": 0,
            "xAxisData": []
        };
        this.options = $.extend({}, this.defaults, opt);

        this.calendarContent = this.options.calendarContent;
        this.clickDay = this.options.clickDay;
        this.slideSwitchCallback = this.options.slideSwitchCallback;
        this.today = this.options.today;
		this.yesterday = this.options.yesterday;
        this.tWeekIndex = this.options.tWeekIndex;
        this.tMonth = this.options.tMonth;
        this.xAxisData = this.options.xAxisData;
        //初始化
        this.init();
    }
    //Calendar
    Calendar.prototype = {
        init: function() {
            var self = this;
            this.createCalendar(this.today, this.tWeekIndex);
        },
        //创建日历
        createCalendar: function(today, tWeekIndex) {
            var self = this;

            var daysArray = [];
            var daysStrArray = [];

            if (window.dateAction === 'week') {
                // 获取当前日期星期*
                var tWeekIndex = this.tWeekIndex;
                var todayInWeek = moment().weekday();

                if (tWeekIndex === 0) {
                    for (var i = 0; i < todayInWeek; i++) {
                        var daysBeforeToday = moment().subtract(todayInWeek - i, 'days');
                        var daysBeforeTodayInDay = daysBeforeToday.date();
                        var daysBeforeTodayStr = daysBeforeToday.year() + '-' + (daysBeforeToday.month() + 1) + '-' + daysBeforeToday.date();
                        daysArray.push(daysBeforeTodayInDay);
                        daysStrArray.push(daysBeforeTodayStr);
                    }


                    for (var n = 0; n < (7 - todayInWeek); n++) {
                        var daysAfterToday = moment().add(n, 'days');
                        var daysAfterTodayInDay = daysAfterToday.date();
                        var daysAfterTodayStr = daysAfterToday.year() + '-' + (daysAfterToday.month() + 1) + '-' + daysAfterToday.date();
                        daysArray.push(daysAfterTodayInDay);
                        daysStrArray.push(daysAfterTodayStr);
                    }
                }
                if (tWeekIndex > 0) {
                    for (var i = 0; i < 7; i++) { 
                        var daysAfterToday = moment().add( i - todayInWeek + 7 * tWeekIndex, 'days');
                        var daysAfterTodayInDay = daysAfterToday.date();
                        var daysAfterTodayStr = daysAfterToday.year() + '-' + (daysAfterToday.month() + 1) + '-' + daysAfterToday.date();
                        daysArray.push(daysAfterTodayInDay);
                        daysStrArray.push(daysAfterTodayStr);
                    }
                }
                if (tWeekIndex < 0) {
                    for (var i = 0; i < 7; i++) {
                        var daysBeforeToday = moment().add(- todayInWeek + i + 7 * tWeekIndex, 'days');
                        var daysBeforeTodayInDay = daysBeforeToday.date();
                        var daysBeforeTodayStr = daysBeforeToday.year() + '-' + (daysBeforeToday.month() + 1) + '-' + daysBeforeToday.date();
                        daysArray.push(daysBeforeTodayInDay);
                        daysStrArray.push(daysBeforeTodayStr);
                    }
                }
            }
              
            if (window.dateAction === 'month') {
                var tMonth = this.tMonth;
                var curMonth = moment().month();
                var nextMonth = moment().month(curMonth + tMonth);
                for ( var i = 0; i < nextMonth.daysInMonth(); i++ ) {
                    var day = nextMonth.year() + '-' + (nextMonth.month() + 1) + '-' + (i + 1);
                    daysStrArray.push(day);
                }
            }
			if (window.dateAction === 'yesterday') {
                var tMonth = this.tMonth;
                var curMonth = moment().month();
                var nextMonth = moment().month(curMonth + tMonth);
                for ( var i = 0; i < nextMonth.daysInMonth(); i++ ) {
                    var day = nextMonth.year() + '-' + (nextMonth.month() + 1) + '-' + (i + 1);
                    daysStrArray.push(day);
                }
            }


               
            // 循环生成x轴数据
            for (var i = 0; i < daysStrArray.length; i++) {
                this.xAxisData[i] = daysStrArray[i].substr(5).split('-').join('.');
            }

            this.daysStrArray = daysStrArray;

            this.clickDay && this.clickDay();
            this.slideSwitchCallback && this.slideSwitchCallback();
            this.slide = false;

            this.slideSwitch(this.calendarContent.get(0), function(dir) {
                if (dir > 0) {
                    self.prevClick();
                } else {
                    self.nextClick();
                }
            });
        },
        //上周
        prevClick: function() {
            if (window.dateAction === 'week') {
                this.tWeekIndex--;
            }
            if (window.dateAction === 'month') {
                this.tMonth--;
            }
            this.createCalendar(this.today, this.tWeekIndex);
        },
        //下周
        nextClick: function() {
            if (window.dateAction === 'week') {
                this.tWeekIndex++;
            }
            if (window.dateAction === 'month') {
                this.tMonth++;
            }
            this.createCalendar(this.today, this.tWeekIndex);
        },
        //左右滑动切换
        slideSwitch: function(obj, callBack) {
            var self = this;
            var start = 0;
            var end = 0;

            $('.report-chart').off('touchstart').on('touchstart',function(e) {
                var touch = e.originalEvent.targetTouches[0]; 
                start = touch.pageX; 
            });
            $('.report-chart').off('touchend').on('touchend',function(e) {
                var touch = e.originalEvent.changedTouches[0]; 
                end = touch.pageX; 
                var dir = end - start;

                if (Math.abs(dir) > 50) {
                    callBack && callBack(dir);
                }
            });
        }
    }
    //在插件中使用Beautifier对象
    $.fn.calendar = function(options) {
        //创建Beautifier的实体
        var calendar = new Calendar(this, options);
        //调用其方法
        return calendar;
    }
})(jQuery, window, document);
