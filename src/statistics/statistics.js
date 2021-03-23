// Collection of functions used in statistics' JavaScript

// Prints the plot in statistics.php
function plotMiscStats() {
    var data = [{
        values: testDiv_vals,
        labels: testDiv_lbls,
        type: "pie",
        name: "Per test",
        title: "Per test",
        sort: false,
        direction: "clockwise",
        domain: {
            row: 0,
            column: 0
        },
        textinfo: "none"
    }, {
        values: studDiv_vals,
        labels: studDiv_lbls,
        type: "pie",
        name: "Per sesso",
        title: "Per sesso",
        domain: {
            row: 0,
            column: 1
        },
        textinfo: "none",
        sort: false,
        direction: "clockwise"
    }, {
        values: classDiv_vals,
        labels: classDiv_lbls,
        type: "pie",
        name: "Per classe",
        title: "Per classe",
        domain: {
            row: 1,
            column: 0
        },
        textinfo: "none",
        sort: false,
        direction: "clockwise",
    }, {
        values: yearDiv_vals,
        labels: yearDiv_lbls,
        type: "pie",
        name: "Per anno",
        title: "Per anno",
        domain: {
            row: 1,
            column: 1
        },
        textinfo: "none",
        sort: false,
        direction: "clockwise"
    }];
    
    var layout = {
        height: "700",
        showlegend: false,
        title: "Suddivisione delle prove",
        grid: {rows: 2, columns: 2}
    };
    
    Plotly.newPlot("cnv", data, layout, {responsive: true});
}

// Test functions

// Plot with values
function draw_graph_val(vals)
{	
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
function draw_graph_box(vals)
{
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
function draw_graph_multibox(graph, add)
{
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
function draw_graph_prc(lbls,vals)
{
    var dgraph = [{
        x: lbls,
        y: vals,
        type: "scatter",
        line: {shape: "spline"}
    }];
    var layout = {
        height: "600",
        title: $("#nomet").html() + " - Valori percentili"
    };
    Plotly.newPlot('cnv',dgraph,layout,{responsive: true});
}

// Update button handlers
$("#update").click(function(){
    getData();
});
$("#graph").change(function(){
    getData();
});

// Ajax function to extract from the DB the requested data 
// int he labels - values format  
function getData()
{
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
        url: "./test_stats_ajax.php",
        data: cond + "&graph=" + $("#graph").val(),
        dataType: "json",   
        async: false,
        success: function(data)	{
            var stats = data[0];
            var records = data[1];
            var graph = data[2];
            
            handleData(stats, records);
            switch($("#graph").val())
            {
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
function handleData(stats,rec)
{
    $("#n").text(stats['n']);
    if(stats['avg'])
        $("#avg").text(stats['avg']);
    if(stats['std'])
        $("#std").text(stats['std']);
    if(stats['med'])
        $("#med").text(stats['med']);

    if(rec['best'])
        $("#best").text(rec['best']);
    if(rec['worst'])
        $("#worst").text(rec['worst']);
    $("#tbest").html(rec['list']);
}
