$(function(){
	function getLayout() {
		return {
				polar: {
					radialaxis: {
						visible: true,
						range: [0, 100]
					}	
				},
				title: "Prove di " + $("#student").text() + " - " + $("#class option:selected").text()
			}
	}

	function drawGraph() {
		Plotly.newPlot("myDiv", data[$("#class").val()], getLayout(), {responsive: true});
	}

	$("#class").on("change", function(){
		drawGraph();
	});
	
	drawGraph();
});