/*
 * Copyright (c) 2016-2018 The City Council of Castelló. 
 * Licensed under the EUPL, Version 1.2 or as soon they will be approved by the European Commission - 
 * subsequent versions of the EUPL (the "Licence"); You may not use this work except in compliance with the 
 * Licence. You may obtain a copy of the Licence at:
 * http://joinup.ec.europa.eu/software/page/eupl Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an "AS IS" basis, WITHOUT WARRANTIES OR CONDITIONS
 * OF ANY KIND, either express or implied. See the Licence for the specific language governing permissions and
 * limitations under the Licence.
 */

/*
D3 Features
The chart employs these D3 features:

d3.csv - load and parse data
d3.scale.ordinal - color encoding
d3.keys - compute column names
d3.svg.arc - display arcs
d3.layout.pie - compute arc angles from data

*/

window.onload = function(){

    // Spinner Loader settings
    var opts = {
      lines: 9, // The number of lines to draw
      length: 9, // The length of each line
      width: 5, // The line thickness
      radius: 14, // The radius of the inner circle
      color: '#EE3124', // #rgb or #rrggbb or array of colors
      speed: 1.9, // Rounds per second
      trail: 40, // Afterglow percentage
      className: 'spinner' // The CSS class to assign to the spinner
    };

    var idtarget = 'd3j-spinner'; // The div name of the html document where the spinner will be placed

    var target = document.getElementById(idtarget);

    var radius = 100,
        padding = 10,
        margin = 10;

    //Color encoding
    var color = d3.scale.ordinal()
        .range(["#337AB7", "#009688", "#FF5252", "#757575"]);

    var arc = d3.svg.arc()
        .outerRadius(radius)
        .innerRadius(radius - 30);

    var pie = d3.layout.pie()
        .sort(null)
        .value(function(d) { return d.cards; });


    function init() {

        // Trigger loader
        var spinner = new Spinner(opts).spin(target);

        // load csv data 
        // The route is from the page that loads this javascript (donut-multiples.js): for example index.php
        d3.csv("data.csv.php", function(data) {

            // Stop the loader
            spinner.stop();

            target.style.height = '1px';

            // Instantiate chart within callback
            chart(data);        

        }); /* csv */
    }

    init();



    function chart(data) {

        color.domain(d3.keys(data[0]).filter(function(key) { return key !== "Label"; }));

        data.forEach(function(d) {
          d.cards = color.domain().map(function(name) {
            return {name: name, cards: +d[name]};
          });
        });

        //d3j-donut-multiples: id name of the html document where the graphic will be placed
        var svg = d3.select("#d3j-donut-multiples").selectAll(".pie")
            .data(data)
          .enter().append("svg")
            .attr("class", "pie")
            .attr("width", (radius * 2)+padding)
            .attr("height", (radius * 2)+padding)

          .append("g")
            .attr("transform", "translate(" + radius + "," + radius + ")");

        svg.selectAll(".arc")
            .data(function(d) { return pie(d.cards); })
          .enter().append("path")
            .attr("class", "arc")
            .attr("d", arc)
            .style("fill", function(d) { return color(d.data.name); });

        svg.append("text")     
          .attr("y", -30)  
          .each(function (d) {
            var arr = d.Label.split(" ");
            for (i = 0; i < arr.length; i++) {
              d3.select(this).append("tspan")
                  .text(arr[i])
                  .attr("dy", i ? "1.2em" : 0)
                  .attr("x", 0)
                  .attr("text-anchor", "middle")
                  .attr("font-family", "Helvetica")
                  .attr("font-size", "16px")
                  .attr("fill", "#000000")
                  .attr("class", "tspan" + i);
            }
          });


    }


    function resize() {
      var width = parseInt(d3.select("#d3j-donut-multiples").style("width")) - margin*2,
      height = parseInt(d3.select("#d3j-donut-multiples").style("height")) - margin*2;
    }


    d3.select(window).on('resize', resize); 

    resize();

}


