fixColumns={
	highest : 0,
	cols: Array(),
	init:function(){
		//if(!NT.isDOM() || !NT.isIE() || NT.isOPERA()) return;
		fixColumns.cols[0] = document.getElementById("colleft");
		fixColumns.cols[1] = document.getElementById("colmid");

		fixColumns.getHighest();
		fixColumns.fix();
	},
	fix:function(){
		for(i=0;i<fixColumns.cols.length;i++){
//			if(fixColumns.cols[i].className != "colmid"){
				fixColumns.cols[i].style.height = parseInt(fixColumns.highest) + 'px ';
//			}
		}
	},
	getHighest:function(){
		for(i=0;i<fixColumns.cols.length;i++){
			if(fixColumns.cols[i].offsetHeight>fixColumns.highest){
				fixColumns.highest = fixColumns.cols[i].offsetHeight;
			}
		}
	}
}
NT.addLoadEvent(fixColumns.init);