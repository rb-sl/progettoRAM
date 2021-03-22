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
