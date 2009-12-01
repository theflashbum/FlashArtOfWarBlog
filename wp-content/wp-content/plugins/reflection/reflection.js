/*****************************************************************
 * BitpressEffectMgr provides functions to apply image effects using
 * Raphael http://raphaeljs.com .
 */
function BitpressEffectMgr(image, height) {
	this.image = image;
	if (height == null) {
		this.reflectionHeight = this.image.height;
	} else {
		if (height.indexOf("%") == height.length - 1) {
			this.reflectionHeight = (height.replace("%", "")) * this.image.height / 100;
		} else {
			this.reflectionHeight = height.replace("px", "") * 1;
		}
	}
	this.raphael = new Raphael(this.image.parentNode, this.image.width, this.image.height + this.reflectionHeight);
}
// Note: the reflection image coordinates and dimension have been optimised for IE, FF, and Opera.
BitpressEffectMgr.prototype.displayDefaultReflection = function() {
	this.raphael.image(this.image.src, 0, 0, this.image.width, this.image.height);
	this.raphael.image(this.image.src, 0, this.image.height - 1, this.image.width + 1, this.image.height).scale(1, -1).attr({opacity: .5});
}
// Note: the gradient rectangle coordinates and dimension have been optimised for IE, FF, and Opera.
BitpressEffectMgr.prototype.displayGradientReflection = function(color) {
	this.displayDefaultReflection();
	var gradient = { type: "linear", dots: [{color: color, opacity: .5}, {color: color}], vector: [0, 0, 0, "100%"] };
    this.raphael.rect(-1, this.image.height - 1, this.image.width + 5, this.reflectionHeight + 5).attr({gradient: gradient, stroke: 'none'});
}

/*****************************************************************
 * BitpressImageMgr processes the images within a document, and applies
 * the reflection effect.
 */
function BitpressImageMgr(gradientBgColor, gradientHeight) {
	this.gradientBgColor = gradientBgColor;
	this.gradientHeight = gradientHeight;
}
BitpressImageMgr.prototype.process = function(images) {
	for (var i = 0; i < images.length; i++) {
	    var classNames = images[i].className.split(" ");
	    for (var j = 0; j < classNames.length; j++) {
	    	if (classNames[j] == "reflection") {
				this.applyEffect(images[i]);
	    	}
	    }
	}
}
BitpressImageMgr.prototype.applyEffect = function(image) {
	this.insertDivWrapper(image);
    if (this.gradientBgColor != null && this.gradientHeight != null) {
    	(new BitpressEffectMgr(image, this.gradientHeight)).displayGradientReflection(this.gradientBgColor);
    } else {
    	(new BitpressEffectMgr(image)).displayDefaultReflection();
    }
	this.hideOrigImage(image);
}
// Inserts div between the image and its parent node.
BitpressImageMgr.prototype.insertDivWrapper = function(image) {
	var div = document.createElement("div");
	var parent = image.parentNode;
	parent.replaceChild(div, image);
	div.appendChild(image);
}
// Hides the original image. Note: using style.display = "none" causes problem on IE.
BitpressImageMgr.prototype.hideOrigImage = function(image) {
	image.style.visibility = "hidden";
	image.parentNode.className = image.className;
	image.width = 0;
	image.height = 0;
}