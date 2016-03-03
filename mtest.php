<?php
/*require('../function.php');
$link = "../index.html";
checkLogin($link);*/

$user_name = "Academic Integrity New - PrePost-LC-AngelaClass";
if(isset($_SESSION['username'])) {
	$user_name = $_SESSION['username'];
}
?>

<!DOCTYPE html>
<meta charset="utf-8">
<head>
<script src="http://d3js.org/d3.v3.min.js"></script>
<!-- <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>  -->
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
</head>

<style>

.node {
  cursor: pointer;
}

.node:hover {
  stroke: #000;
  stroke-width: 1.5px;
}

.node--root {
  fill: rgb(31, 119, 180);   /* Color=Deep blue */
  fill-opacity: .25;
  stroke: rgb(31, 119, 180);  /* stroke=circle line */
  stroke-width: 1px;
}

.node--leaf {
  fill: #ff7f0e;
}

.node--ultraleaf {
  fill: rgb(200, 200, 200);  /* Color=Gray */
  fill-opacity: 1;
}

.label,
.ultralabel {
  text-anchor: middle;
}

.label,
.ultralabel,
.node--root
{
  font-family: "Times New Roman", Times, serif;
}

/*#DLG {
    border: outset;
    border-radius: 100px;
    height: 30px;
    width: 200px;
	text-align: center;
    vertical-align: middle;
    padding-top: 10px;
    background: linear-gradient(rgb(218, 253, 167), rgb(228, 253, 194), rgb(245, 255, 230));
    position: relative;
    top: -800px;
    left: 1000px;
}

#DLG:active {
	border: inset;
    background: linear-gradient(rgb(255, 190, 134), rgb(255, 208, 170), rgb(255, 235, 219));
}

#DLG:hover {
    cursor: pointer;
}*/

	.axis {
	  font: 10px sans-serif;
	}
	
	.axis path,
	.axis line {
	  fill: none;
	  stroke: #000;
	  shape-rendering: crispEdges;
	}

</style>
<body>
<script>

var margin1 = 20,
    diameter = 960;

var color = d3.scale.linear()
    .domain([-1, 5])
    .range(["hsl(152,80%,80%)", "hsl(228,30%,40%)"])
    .interpolate(d3.interpolateHcl);

var pack = d3.layout.pack()
    .padding(2)
    .size([diameter - margin1, diameter - margin1])     //Graph's size
    .value(function(d) { return 1+5*d.size; })        //leaf circle's size, minimum is 1, "d" means leaf nodes

var chart1 = d3.select("body").append("svg")        //Insert "svg" tag to html
    .attr("width", diameter)
    .attr("height", diameter)
    .attr("style","float:left")
    .attr("id", "chart1")
    .append("g")
    .attr("transform", "translate(" + diameter / 2 + "," + diameter / 2 + ")");


//Setting of Chart2
var margin2 = {top: 100, right: 20, bottom: 70, left: 40},
width = 500 - margin2.left - margin2.right,
height = 500 - margin2.top - margin2.bottom;

//Parse the date / time
var	parseDate = d3.time.format("%Y-%m").parse;

var x = d3.scale.ordinal().rangeRoundBands([0, width], .05);

var y = d3.scale.linear().range([height, 0]);

var xAxis = d3.svg.axis()
.scale(x)
.orient("bottom");

var yAxis = d3.svg.axis()
.scale(y)
.orient("left")
.ticks(10);

var chart2 = d3.select("body").append("svg")
.attr("width", width + margin2.left + margin2.right)
.attr("height", height + margin2.top + margin2.bottom)
.attr("id", "chart2")
.append("g")
.attr("transform", 
      "translate(" + margin2.left + "," + margin2.top + ")");




d3.json("<?php echo $user_name; ?>" + ".json", function(error, root) {
  if (error) throw error;

  var focus = root,
      nodes = pack.nodes(root),
      view;

  var circle = chart1.selectAll("circle")
      .data(nodes)
      .enter().append("circle")
      .attr("class", function(d) { return d.parent ? (d.children ? "node" : (d.size > 0 ? "node node--leaf" : "node node--ultraleaf")) : "node node--root"; })  //check and assign the nodes' levels
      .style("fill", function(d) { return d.children ? color(d.depth) : null; })
      .on("click", function(d, i) { if (focus !== d) zoom(d, i); d3.event.stopPropagation(); });

  var text = chart1.selectAll("text")
      .data(nodes)
      .enter().append("text")
      .attr("class", function(d) { return d.parent ? (d.children ? "label" : (d.size > 0 ? "label" : "ultralabel")) : "label"; })
      .style("fill-opacity", function(d) { return d.parent === root ? 1 : 0; })
      .style("display", function(d) { return d.parent === root ? null : "none"; });
  
  var names = text.each(function (d) { 
	  	  var firstseg = d.name.split(/\//g)[0];
  	  	  var tokens = firstseg.match(/\S+/g); 
  	  	  
		  if (tokens.length < 3)
			  if (d.children || tokens.length === 1)
			  		d3.select(this).append("tspan").text(function (d) { return firstseg.trim();; });
			  else {
				  	d3.select(this).append("tspan").text(function (d) { return tokens[0]; });
				  	d3.select(this).append("tspan").text(function (d) { return tokens[1]; })
	    				.attr("x", "0")
	    				.attr("dy", "1em");	
			  }
		  else {
			  for (i = 0; i < tokens.length;) { 
					if (i < 2)	  
						d3.select(this).append("tspan").text(function (d) { return tokens[i] + " " + tokens[i+1]; });
				    else if ((tokens.length - i) === 1)
				    	d3.select(this).append("tspan").text(function (d) { return tokens[i]; })
			    			.attr("x", "0")
			    			.attr("dy", "1em");		
				    else
				    	d3.select(this).append("tspan").text(function (d) { return tokens[i] + " " + tokens[i+1]; })
			    			.attr("x", "0")
			    			.attr("dy", "1em");			    
				    i = i + 2;
			  }
	      }
      }
  );
  
  var sizes = text.append("tspan")
	  .attr("x", "0")
	  .text(function(d) { return d.size; });

  circle.append("svg:title")
  .text(function(d) { return d.name + (d.children ? "" : ": " + d.size); });    //assign display information to the circle*/

  var node = chart1.selectAll("circle,text");

  d3.select("#chart1")
      .on("click", function() { zoom(root); });

  zoomTo([root.x, root.y, root.r * 2 + margin1]);

  jQuery.fn.d3Click = function () {
	  this.each(function (i, e) {
	    var evt = document.createEvent("MouseEvents");
	    evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);

	    e.dispatchEvent(evt);
	  });
  };

  $("#chart1").d3Click();
  
  function zoom(d, i) {
    var focus0 = focus; focus = d;
    
    var transition = d3.transition()
        .duration(d3.event.altKey ? 7500 : 750)
        .tween("zoom", function(d) {
          var i = d3.interpolateZoom(view, [focus.x, focus.y, focus.r * 2 + margin1]);
          return function(t) { zoomTo(i(t)); };
        });

   transition.selectAll("text")  //control the appear or disappear of texts
      .filter(function(d) { return d.parent === focus || this.style.display === "inline" || (!d.children && d === focus); })
      .style("fill-opacity", function(d) { return d.parent === focus ? 1 : ((!d.children && d === focus) ? 1 : 0) ; })   //focus text related
      .each("start", function(d) { if (d.parent === focus) this.style.display = "inline"; })
      .each("end", function(d) { if (d.parent !== focus) this.style.display = "none"; if (!d.children && d === focus) this.style.display = "inline"; });
  }
  
  function zoomTo(v) {
    var k = diameter / v[2]; view = v;
    node.attr("transform", function(d) { return "translate(" + (d.x - v[0]) * k + "," + (d.y - v[1]) * k + ")"; });
    circle.attr("r", function(d) { return d.r * k; });   //control circles' sizes

    //Calculate the font-size according to the circles' sizes
    text.style("font-size", function (d) { 
    	var firstseg = d.name.split(/\//g)[0];
    	var wordpixel = d.r * k * 2 * 0.8/firstseg.trim().length;  //assumed font size
    	        
    	if (!d.children && d === focus && wordpixel < 31) {
    		return wordpixel * 2.5;
    	}
    	else if (d.parent && d.children && wordpixel < 30) { 
    		magnify = 19.617 * Math.pow(wordpixel,-0.868);
        	return wordpixel * magnify; }
    	else if ( firstseg.match(/[a-zA-Z]/g) === null) {  //if it is Chinese phrase, there is different magnification
			if (d.parent===focus && !d.children && wordpixel < 12) {
				magnify = 3.6133 * Math.pow(wordpixel,-0.425);
	        	return wordpixel * magnify; 
	        } else { return wordpixel; }
    	} else if (d.parent===focus && !d.children && wordpixel < 11) {  //resolution difference affects last level 2 font size? home resolution: 1366 x 768
        	magnify = 9.6474 * Math.pow(wordpixel,-0.6977);
        	return wordpixel * magnify;
        }
    	else {
    		return wordpixel;
    	}
    });
    
    sizes.attr("dy", function (d) { 
    	var firstseg = d.name.split(/\//g)[0];
    	var wordpixel = d.r * k * 2 * 0.8/firstseg.trim().length;  //assumed font size

        //Calculate line distance
    	if (!d.children && d === focus && wordpixel < 31) {
    		return wordpixel * 2.5;
    	}
    	else if (wordpixel < 14 ){
			lindis = 23.13 * Math.pow(wordpixel,-1.117);
    	    return wordpixel * lindis; 
    	} else {
    		return wordpixel;
        }
    });
  }

  circle.on("mouseover", function (d) { compbc(d); })
  .on("mouseout", function(d) { circle.style("fill", function(d) { return d.children ? color(d.depth) : null; }); chart2.selectAll("*").remove();});

  var chilist = [];
  function compbc(d) {

	  preid = d.id;
	  
	  circle.style("fill", function(d) { return d.id === preid ? "red" : color(d.depth); })
	       
	  count = 0;

	  for (i = 0; i < nodes.length; i++) {
		  if (nodes[i].id === d.id) {
			chilist[count] = {id:count,name:nodes[i].name,size:nodes[i].size,status:(count===0? "Pre" : "Post")};
		  	count++;
		  }
	  }

	 chart2.selectAll("text")
	    .data(chilist)
	    .enter().append("text")
	    .attr("y", function(d) { return y(d.size); })
	    .attr("x", function(d) { return 100 + d.id*210; })
	    .attr("dx", -5)
	    .attr("dy", ".36em")
	    .attr("text-anchor", "end")
	    .text(function(d) { return d.size; });

	 chart2.selectAll("bar")
	    .data(chilist)
    	.enter().append("rect")
		.style("fill", "steelblue")
		.attr("width", 40)
	    .attr("x", function(d) { return 100 + d.id*210; })
     	.attr("y", function(d) { return y(d.size); })
     	.attr("height", function(d) { return height - y(d.size); });

	  chart2.append("g")
	  	.attr("class", "x axis")
	  	.attr("transform", "translate(0," + height + ")")
	  	.call(xAxis);

	  chart2.append("g")
      	.attr("class", "y axis")
	    .call(yAxis);
      
	  x.domain(chilist.map(function(d) { return d.status; }));
	  y.domain([0, d3.max(nodes, function(d) { return d.size; })]);
  }

  
	
});

function DLgraph() { 
   var e = document.createElement('script'); 
   e.setAttribute('src', 'https://nytimes.github.io/svg-crowbar/svg-crowbar.js'); 
   e.setAttribute('class', 'svg-crowbar'); 
   document.body.appendChild(e); 
};

d3.select(self.frameElement).style("height", diameter + "px");

</script>

<div id="DLG" onclick="">Show the comparsion by level</div>
