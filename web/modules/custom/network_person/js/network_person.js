(function ($, Drupal, drupalSettings) {
  let initialized;
  function init() {
    if(!initialized) {
      initialized = true;
      const w = 2200;
      const h = 2200;
      const linkDistance = 150;
      const div = 'network_person';
      const dataset = drupalSettings.network_person.graphdata;
      const colors = d3.scale.category10();

      let svg = d3.select('#' + div)
        .classed("svg-container", true)
        .append("svg")
        // Responsive SVG needs these 2 attributes and no width and height attr.
        .attr('width', '100%')
        .attr('height', '100%')
        .attr("preserveAspectRatio", "xMinYMin meet")
        .attr("viewBox", "0 0 " + w + " " + h)
        // Class to make it responsive.
        .classed("svg-content-responsive", true)
        .call(d3.behavior.zoom().on("zoom", function () {
          svg.attr("transform", "translate(" + d3.event.translate + ")" + " scale(" + d3.event.scale + ")")
        }))
        .append("g")

      let force = d3.layout.force()
        .nodes(dataset.nodes)
        .links(dataset.edges)
        .size([w,h])
        .linkDistance([linkDistance])
        .charge([-500])
        .theta(0.1)
        .gravity(0.05)
        .start();

      let edges = svg.selectAll("line")
        .data(dataset.edges)
        .enter()
        .append("line")
        .attr("id",function(d,i) {return 'edge'+i})
        .attr('marker-end','url(#arrowhead)')
        .style("stroke","#ccc")
        .style("pointer-events", "none");

      let nodes = svg.selectAll("circle")
        .data(dataset.nodes)
        .enter()
        .append("circle")
        .attr({"r":15})
        //.style("fill",function(d,i){return colors(i);})
        .style("fill", "steelblue")
        .call(force.drag)
        .on('click', connectedNodes);

      let nodelabels = svg.selectAll(".nodelabel")
        .data(dataset.nodes)
        .enter()
        .append("a").attr("xlink:href", function(d){return d.link;})
        .append("text")
        .attr({"class":"nodelabel",
          'font-size':12,
          "stroke":"black"})
        .text(function(d){return d.name;});

      let edgepaths = svg.selectAll(".edgepath")
        .data(dataset.edges)
        .enter()
        .append('path')
        .attr({'d': function(d) {return 'M '+d.source.x+' '+d.source.y+' L '+ d.target.x +' '+d.target.y},
          'class':'edgepath',
          'fill-opacity':0,
          'stroke-opacity':0,
          'fill':'blue',
          'stroke':'red',
          'id':function(d,i) {return 'edgepath'+i}})
        .style("pointer-events", "none");

      force.on("tick", function(){
        edges.attr({"x1": function(d){return d.source.x;},
          "y1": function(d){return d.source.y;},
          "x2": function(d){return d.target.x;},
          "y2": function(d){return d.target.y;}
        });

        nodes.attr({"cx":function(d){return d.x;},
          "cy":function(d){return d.y;}
        });

        nodelabels.attr("x", function(d) { return d.x + 16; })
          .attr("y", function(d) { return d.y + 3; });

        edgepaths.attr('d', function(d) { let path='M '+d.source.x+' '+d.source.y+' L '+ d.target.x +' '+d.target.y;
          return path});
      });


      //------------------------------------------------------------
      //Toggle stores whether the highlighting is on
      let toggle = 0;
      //Create an array logging what is connected to what
      let linkedByIndex = {};
      for (i = 0; i < dataset.nodes.length; i++) {
        linkedByIndex[i + "," + i] = 1;
      };
      dataset.edges.forEach(function (d) {
        linkedByIndex[d.source.index + "," + d.target.index] = 1;
      });
      //This function looks up whether a pair are neighbours
      function neighboring(a, b) {
        return linkedByIndex[a.index + "," + b.index];
      }
      function connectedNodes() {
        if (toggle == 0) {
          //Reduce the opacity of all but the neighbouring nodes
          d = d3.select(this).node().__data__;
          nodes.style("opacity", function (o) {
            return neighboring(d, o) | neighboring(o, d) ? 1 : 0.05;
          });
          edges.style("opacity", function (o) {
            return d.index == o.source.index | d.index == o.target.index ? 1 : 0.1;
          });
          //Reduce the op
          toggle = 1;
        } else {
          //Put them back to opacity=1
          nodes.style("opacity", 1);
          edges.style("opacity", 1);
          toggle = 0;
        }
      }
    }
  }

  Drupal.behaviors.network_person = {
    attach: function (context, settings) {
      init();
    }
  };
})(jQuery, Drupal, drupalSettings);
