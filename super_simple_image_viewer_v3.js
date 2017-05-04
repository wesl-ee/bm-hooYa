(function(w, d) {
  // simple function to get element by ID
  function _getEl(el) {
    if ('string' === typeof(el)) {
      var sError = 'element' + el + ' does not exist';
      el = d.getElementById(el);

      if (! el) {
        alert(sError);
      }
    }

    return el;
  }

  /**
   * Instantiation function for the SimpleImageViewer.
   * @param elImage {String|Element} Required. The ID or element instance for the image element to be updated.
   * @param conf {Object} Optional. Additional configuration options:
   *    caption - The ID or element instance for the caption element to be updated.
   *    defaultGroup - The label for your default group of images. If you have just one collection of images,
   *      just leave this blank.
   *    next - The ID or element instance for the element to trigger changing to the next image.
   *    previous - The ID or element instance for the element to trigger changing to the previous image.
   *    timeout - The number of milliseconds between changing the image, when using the autoplay feature.
   *  Although, `next` and `previous` are optional, you will probably want to include them, or users won't be
   *  able to move through your images.
   * @constructor
   */
  function SimpleImageViewer(elImage, conf) {
    var that = this;
    if (!conf) {
      conf = {};
    }

    that.boolInit = false;

    that.sDefaultGroup = conf.defaultGroup || 'group0';
    that.sCurrentGroup = that.sDefaultGroup;

    that.elCaption = _getEl(conf.caption);
    that.elNext = _getEl(conf.next);
    that.elPrevious = _getEl(conf.previous);
    that.elImage = _getEl(elImage);
//    that.elJump = _getEl(conf.jump);

    that.nIndex = 0;
    that.nIntervalId = 0;
    that.nIntervalTimeout = conf.timeout || 3000;

    that.oPhotos = {};
    that.oPhotos[that.sDefaultGroup] = [];

    // handle next click
    if (that.elNext) {
      that.elNext.onclick = function(e) {
        e.preventDefault();
        that.next();
      };
    }

    // handle previous click
    if (that.elPrevious) {
      that.elPrevious.onclick = function(e) {
        e.preventDefault();
        that.previous();
      };
    }
  }

  SimpleImageViewer.prototype = {
    /**
     * Adds a photo to the object. By default all images are added to a single
     * @param sPhotoPath {String} Required. The path to this photo.
     * @param opts {Object} Optional. Additional data and organizational options:
     *    alt - Alternative copy for accessibility (will use caption, if not provided).
     *    caption - A caption for this photo.
     *    group - Use groups to organize photos into collections. The SimpleImageViewer will be able to navigate
     *      through one collection at a time. Most people just have a single collection, but this allows you
     *      to organize your images. You will need to manually call changeGroup(sGroupName) to switch between image
     *      groups.
     */
    addPhoto: function(sPhotoPath, opts) {
      var sAlt = '',
        sCaption = '',
        sGroup = this.sDefaultGroup,  // default group for a single group of images
        oImage,
        aPhotos;

      if (opts) {
        sAlt = opts.alt || opts.caption || sAlt;
        sCaption = opts.caption || sCaption;
        sGroup = opts.group || sGroup;
      }

      aPhotos = this.oPhotos[sGroup] || [];
      oImage = new Image();
      oImage.src = sPhotoPath;
      aPhotos.push({alt: sAlt, caption: sCaption, img: oImage});
      this.oPhotos[sGroup] = aPhotos;

      if (! this.boolInit) {
        this.boolInit = true;
        this.updateImage();
      }
    },

    /**
     * Change to a different collection of photos.
     * @param sChangeGroup {String} Required. The group name to change to.
     */
    changeGroup: function(sChangeGroup) {
      this.nIndex = 0;
      this.sCurrentGroup = sChangeGroup;
      this.updateImage();
    },

    /**
     * Move to the next image in the current group.
     */
    next: function() {
      this.nIndex += 1;
//      document.location.hash = "#" + this.nIndex;
      this.updateImage();

      if (this.nIntervalId) {
        this.stop();
        this.play();
      }
    },
    /**
     * Jump to an image in the array
     */
     toPage: function(page) {
       
       this.nIndex = page;
       this.updateImage();


       if (this.nIntervalId) {
         this.stop();
         this.play();
      }
    },

    /**
     * Start the autoplay feature. You can control the timeout by setting the `timeout` configuration option
     * when instantiating the SimpleImageViewer object.
     */
    play: function() {
      var that = this;

      if (!that.nIntervalId) {
        this.nIntervalId = setInterval(function() {
          that.nIndex += 1;
          that.updateImage();
        }, that.nIntervalTimeout);
      }
    },

    /**
     * Move to the previous image in the current group.
     */
    previous: function() {
      this.nIndex -= 1;
//      document.location.hash = "#" + this.nIndex;
      this.updateImage();

      if (this.nIntervalId) {
        this.stop();
        this.play();
      }
    },

    /**
     * Stop the autoplay feature.
     */
    stop: function() {
      clearInterval(this.nIntervalId);
      this.nIntervalId = 0;
    },

    /**
     * Called when the source of the image element needs to change.
     */
    updateImage: function() {
      var aImages = this.oPhotos[this.sCurrentGroup],
        oImage;

      // no images in this photo group
      if (! (aImages && aImages.length)) {
        alert('No images available in group=' + this.sCurrentGroup);
      }

      // went too far previous, move to end of array
      if (0 > this.nIndex) {
        this.nIndex = aImages.length - 1;
      }

      // went too far forward, move to head of array
      if (this.nIndex >= aImages.length) {
        this.nIndex = 0;
      }

      oImage = aImages[this.nIndex];
      this.elImage.src = oImage.img.src;

      // update alternative copy
      if (oImage.alt) {
        this.elImage.alt = oImage.alt;
      }

      if (this.elCaption) {
        this.elCaption.innerHTML = oImage.caption;
      }
    }
  };

  w.SimpleImageViewer = SimpleImageViewer;
}(window, document));