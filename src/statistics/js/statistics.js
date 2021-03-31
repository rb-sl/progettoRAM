// Collection of functions used in statistics.php

// Prints the plot in statistics.php
function plotMiscStats() {
    Plotly.react("cnv", getPlotData(), getPlotLayout(), {responsive: true});
}

// Animation of the plot
function reloadPlot() {
    Plotly.animate("cnv", {
            data: getPlotData(),
            layout: getPlotLayout()
        }, {
            transition: {
            duration: 500,
            easing: "cubic-in-out"
        },
        frame: {
            duration: 500
        }
    });
}

function getPlotData() {
    return [{
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
}

function getPlotLayout() {
    return {
        height: "700",
        showlegend: false,
        title: "Suddivisione delle prove",
        grid: {
            rows: 2, 
            columns: 2
        }
    }
}

// Update button handler
$("#update").click(function() {
    if($(this).hasClass("btn-warning")) {
        getData();
    }
});

// Function to update the page's data
function getData() {
    if(!checkYears()) {
        return;
    }

    disableUpdate();

    cond = buildCondFromMenu();    
    $.ajax({  
        url: "/statistics/statistics_ajax.php",
        data: cond,
        dataType: "json",
        success: function(data)	{
            // Update of shown statistics
            $("#stud_tot").text(data.stud_tot);
            $("#res_tot").text(data.res_tot);

            if(data["stud_num"] !== undefined) {
                $("#stud_num").text(data.stud_num);
                $("#stud_perc").text(data.stud_perc);

                $("#res_num").text(data.res_num);
                $("#res_perc").text(data.res_perc);
            }
            else {
                $("#stud_num").text(data.stud_tot);
                $("#stud_perc").text("100");

                $("#res_num").text(data.res_tot);
                $("#res_perc").text("100");
            }

            // Reloads the plot with the new data
            testDiv_vals = data.test.vals;
            testDiv_lbls = data.test.lbls;

            studDiv_vals = data.stud.vals;
            studDiv_lbls = data.stud.lbls;

            classDiv_vals = data.class.vals;
            classDiv_lbls = data.class.lbls;

            yearDiv_vals = data.year.vals;
            yearDiv_lbls = data.year.lbls;
                       
            reloadPlot();
            enableUpdate();
        },
        error: function() {
            alert("Errore ottenimento dati aggiornati");
        },
        timeout: 5000
    });
}
