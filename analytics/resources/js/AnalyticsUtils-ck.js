var AnalyticsUtils={getColumns:function(e){var t=[];$.each(e.apiResponse.columnHeaders,function(n,r){$type="string";if(r.name=="ga:date")$type="date";else if(r.name=="ga:latitude")$type="number";else if(r.name=="ga:longitude")$type="number";else if(r.dataType=="INTEGER"||r.dataType=="PERCENT"||r.dataType=="TIME")$type="number";typeof e.widget!="undefined"&&e.widget.settings.options.chartType=="PieChart"&&n==0&&($type="string");t[n]={type:$type,dataType:r.dataType,name:r.name}});return t},getRows:function(response){var rows=[],columns=this.getColumns(response);$.each(response.apiResponse.rows,function(k,row){var cells=[];$.each(columns,function(k2,column){var cell=response.apiResponse.rows[k][k2];if(column.type=="date"){$date=cell;$year=eval($date.substr(0,4));$month=eval($date.substr(4,2))-1;$day=eval($date.substr(6,2));newDate=new Date($year,$month,$day);cell=newDate}else if(column.dataType=="INTEGER"||column.name=="ga:latitude"||column.name=="ga:longitude")cell=eval(cell);else if(column.dataType=="PERCENT")cell={f:Math.round(eval(cell)*100)/100+" %",v:eval(cell)};else if(column.dataType=="TIME")cell={f:eval(cell)+" seconds",v:eval(cell)};else if(column.name=="ga:continent"||column.name=="ga:subContinent")cell.v=""+cell.v;cells[k2]=cell});rows[k]=cells});return rows}};