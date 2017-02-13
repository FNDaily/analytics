Analytics.Realtime=Garnish.Base.extend({calcTotal:null,calcNewVisitor:null,calcReturningVisitor:null,$element:null,$title:null,$body:null,$spinner:null,$streamstatus:null,$error:null,$activeVisitorsCount:null,$progress:null,$legend:null,$realtimeVisitors:null,$newVisitorsProgress:null,$newVisitorsValue:null,$returningVisitorsProgress:null,$returningVisitorsValue:null,timer:null,settings:null,init:function(s,i){this.setSettings(i),this.$element=$("#"+s),this.$title=$(".title",this.$element),this.$body=$(".body",this.$element),this.$spinner=$(".spinner",this.$element),this.$streamstatus=$(".streamstatus",this.$element),this.$error=$(".error",this.$element),this.$activeVisitorsCount=$(".active-visitors .count",this.$realtimeVisitors),this.$progress=$(".progress",this.$realtimeVisitors),this.$legend=$(".legend",this.$realtimeVisitors),this.$realtimeVisitors=$(".analytics-realtime-visitors",this.$element),this.$newVisitorsProgress=$(".progress-bar.new-visitors",this.$realtimeVisitors),this.$newVisitorsValue=$(".progress-bar.new-visitors span",this.$realtimeVisitors),this.$returningVisitorsProgress=$(".progress-bar.returning-visitors",this.$realtimeVisitors),this.$returningVisitorsValue=$(".progress-bar.returning-visitors span",this.$realtimeVisitors),this.timer=!1,this.start(),setInterval($.proxy(function(){this.$streamstatus.hasClass("hidden")?this.$streamstatus.removeClass("hidden"):this.$streamstatus.addClass("hidden")},this),1e3),this.addListener(Garnish.$win,"resize","_handleWindowResize")},_handleWindowResize:function(){this.$newVisitorsValue.innerWidth()>this.$newVisitorsProgress.width()?this.$newVisitorsValue.addClass("hidden"):this.$newVisitorsValue.removeClass("hidden"),this.$returningVisitorsValue.innerWidth()>this.$returningVisitorsProgress.width()?this.$returningVisitorsValue.addClass("hidden"):this.$returningVisitorsValue.removeClass("hidden")},start:function(){this.timer&&this.stop(),this.request(),console.log("refreshInterval",this.settings.refreshInterval),this.timer=setInterval($.proxy(function(){this.request()},this),1e3*this.settings.refreshInterval)},stop:function(){clearInterval(this.timer)},request:function(){this.$spinner.removeClass("body-loading"),this.$spinner.removeClass("hidden"),Craft.queueActionRequest("analytics/reports/realtime-widget",{},$.proxy(function(s,i){if("success"==i&&"undefined"==typeof s.error)this.$error.addClass("hidden"),this.$realtimeVisitors.removeClass("hidden"),this.handleResponse(s);else{var t="An unknown error occured.";"undefined"!=typeof s&&s&&"undefined"!=typeof s.error&&(t=s.error),this.$realtimeVisitors.addClass("hidden"),this.$error.html(t),this.$error.removeClass("hidden")}this.$spinner.addClass("hidden")},this))},handleResponse:function(s){var i=s.newVisitor,t=s.returningVisitor;this.calcTotal=1*t+1*i,this.$activeVisitorsCount.text(this.calcTotal),this.calcTotal>0?(this.$progress.removeClass("hidden"),this.$legend.removeClass("hidden")):(this.$progress.addClass("hidden"),this.$legend.addClass("hidden")),this.calcTotal>0?this.calcNewVisitor=Math.round(100*i/this.calcTotal):this.calcNewVisitor=100,this.calcReturningVisitor=100-this.calcNewVisitor,this.$newVisitorsProgress.css("width",this.calcNewVisitor+"%"),this.$newVisitorsProgress.attr("title",this.calcNewVisitor+"%"),this.$newVisitorsValue.text(this.calcNewVisitor+"%"),this.$newVisitorsValue.innerWidth()>this.$newVisitorsProgress.width()&&this.$newVisitorsValue.addClass("hidden"),this.calcNewVisitor>0?this.$newVisitorsProgress.removeClass("hidden"):this.$newVisitorsProgress.addClass("hidden"),this.$returningVisitorsProgress.css("width",this.calcReturningVisitor+"%"),this.$returningVisitorsProgress.attr("title",this.calcReturningVisitor+"%"),this.$returningVisitorsValue.text(this.calcReturningVisitor+"%"),this.$returningVisitorsValue.innerWidth()>this.$returningVisitorsProgress.width()&&this.$returningVisitorsValue.addClass("hidden"),this.calcReturningVisitor>0?this.$returningVisitorsProgress.removeClass("hidden"):this.$returningVisitorsProgress.addClass("hidden")}},{defaults:{refreshInterval:15}});