// Collection of functions used for test_stats.php

// Plot with values
function draw_graph_val(vals) {	
    var dgraph = [{
        x: vals,
        type: "histogram",
        marker: {
            line: {
                width: 1,
                opacity: 0
            }
        }
    }];

    var layout = {
        height: "600",
        title: $("#nomet").html() + " - Valori"
    };

    Plotly.newPlot("cnv", dgraph, layout, {responsive: true});
}

// Function to draw a single box plot
function draw_graph_box(vals) {
    var trace1 = {
        x: vals,
        type: "box",
        boxpoints: false,
        boxmean: true,
        hoverinfo: "x"
    };

    var data = [trace1];

    var layout = {
        height: "600",
        title: $("#nomet").html() + " - Box plot",
        yaxis: {
            visible: false
        }
    };

    Plotly.newPlot("cnv", data, layout, {responsive: true});	
}

// Function to draw many box plots
function draw_graph_multibox(graph, add) {
    var data = [];
    $.each(graph, function(key, val){
        if(add == "hbox")
            key = key + "/" + (parseInt(key) + 1);
        data.push({
            y: val,
            type: "box",
            boxpoints: false,
            boxmean: true,
            hoverinfo: "y",
            name: key
        })
    });
    var layout = {
        height: "600",
        title: $("#nomet").html() + " - " + $("#graph option:selected").html()
    };

    Plotly.newPlot("cnv", data, layout, {responsive: true});
}

// Function to deaw the percentiles graph
function draw_graph_prc(lbls,vals) {
    var dgraph = [{
        x: lbls,
        y: vals,
        type: "scatter",
        line: {
            shape: "spline"
        }
    }];
    var layout = {
        height: "600",
        title: $("#nomet").html() + " - Valori percentili"
    };
    Plotly.newPlot("cnv", dgraph, layout, {responsive: true});
}

// Update button handlers
$("#update").click(function() {
    getData();
});
$("#graph").change(function() {
    getData();
});

// Ajax function to extract from the DB the requested data 
// int he labels - values format  
function getData() {
    if(!checkYears())
        return;

    cond = buildCondFromMenu();
    
    $("#update").attr("disabled", true);
    $("#graph").attr("disabled", true);
    
    $("#n").text("-");
    $("#avg").text("-");
    $("#med").text("-");
    $("#std").text("-");

    $("#best").text("-");
    $("#worst").text("-");
    $(".rcr").text("-");    

    $.ajax({  
        url: "/statistics/test_stats_ajax.php",
        data: "id=" + id + "&graph=" + $("#graph").val() + cond,
        dataType: "json",   
        async: false,
        success: function(data)	{
            var stats = data[0];
            var records = data[1];
            var graph = data[2];
            
            handleData(stats, records);
            switch($("#graph").val()) {
                case "val":
                    draw_graph_val(graph['vals']);
                    break;
                case "box":
                    draw_graph_box(graph['vals']);
                    break;
                case "prc":
                    draw_graph_prc(graph['lbls'], graph['vals']);
                    break;
                case "hbox":
                case "cbox":
                case "sbox":
                    draw_graph_multibox(graph, $("#graph").val());
                    break;
            }
            $("#graph").attr("disabled", false);
            $("#update").removeClass("btn-warning");
            $("#update").addClass("btn-primary");
            $("#update").attr("disabled", false);
        },
        error: function() {
            alert("Errore ottenimento dati aggiornati");
        },
        timeout: 5000
    });
}

// Data update function
function handleData(stats, rec) {
    $("#n").text(stats['n']);
    if(stats['avg'])
        $("#avg").text(Math.round(stats['avg'] * 100) / 100);
    if(stats['std'])
        $("#std").text(Math.round(stats['std'] * 100) / 100);
    if(stats['med'])
        $("#med").text(Math.round(stats['med'] * 100) / 100);

    if(rec['best'])
        $("#best").text(Math.round(rec['best'] * 100) / 100);
    if(rec['worst'])
        $("#worst").text(Math.round(rec['worst'] * 100) / 100);
    $("#tbest").html(rec['list']);
}
