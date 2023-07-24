function chart(element,type,key,labels,data){
    opt={
        element: element,
        resize: true,
        data: data,
        xkey: 'x',
        ykeys: key,
        labels: labels,
        hideHover: 'auto',
        xLabelMargin: 1
    };
    if (type=='b'){var newChart = new Morris.Bar(opt);}
    if (type=='l'){var newChart = new Morris.Line(opt);}
    if (type=='a'){var newChart = new Morris.Area(opt);}
}

function chart2(element,data){
	opt={
		element: element,
    	resize: true,
    	data: data
    };
    var donut = new Morris.Donut(opt);
}