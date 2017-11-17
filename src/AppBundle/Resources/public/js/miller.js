Function.prototype.bind || (Function.prototype.bind = function (t) {
    if ("function" != typeof this) throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");
    var i = Array.prototype.slice.call(arguments, 1),
        e = this,
        s = function () {},
        n = function () {
            return e.apply(this instanceof s && t ? this : t, i.concat(Array.prototype.slice.call(arguments)))
        };
    return s.prototype = this.prototype, n.prototype = new s, n
}), "undefined" == typeof window.MILLER && (window.MILLER = {}), "undefined" == typeof window.MILLER.support && (window.MILLER.support = {}), window.MILLER.support.history = function () {
    return window.history && window.history.pushState && window.history.replaceState
},
    function () {
        "use strict";

        function t(t) {
            0 !== t.$el.length && MILLER.support.history() && (i(window).width() < 640 || (this.$el = t.$el, this.$root = this.$el.find("#root"), this.$section = this.$el.find("#section"), this.$subsection = this.$el.find("#subsection"), this.$breadcrumbs = i("#global-breadcrumb ol"), this.animateSpeed = 330, 0 === this.$section.length && (this.$section = i('<div id="section" class="pane with-sort" />'), this.$el.prepend(this.$section), this.$el.addClass("section")), 0 === this.$subsection.length ? (this.$subsection = i('<div id="subsection" class="pane" />').hide(), this.$el.prepend(this.$subsection)) : this.$subsection.show(), this.displayState = this.$el.data("state"), "undefined" == typeof this.displayState && (this.displayState = "root"), this._cache = {}, this.lastState = this.parsePathname(window.location.pathname), this.$el.on("click", "a", this.navigate.bind(this)), i(window).on("popstate", this.popState.bind(this))))
        }
        window.MILLER = window.MILLER || {};
        var i = window.jQuery;
        t.prototype = {
            popState: function (t) {
                var i, e = t.originalEvent.state;
                e || (e = this.parsePathname(window.location.pathname)), this.lastState.slug !== e.slug && (i = "" == e.slug ? this.showRoot() : e.subsection ? this.restoreSubsection(e) : this.loadSectionFromState(e, !0), i.done(function () {
                    this.trackPageview(e)
                }.bind(this)))
            },
            restoreSubsection: function (t) {
                if (this.lastState.section != t.section) {
                    var i = window.location.pathname.split("/").slice(0, -1).join("/"),
                        e = this.parsePathname(i),
                        s = this.loadSectionFromState(e, !0);
                    return s.pipe(function () {
                        return this.loadSectionFromState(t, !0)
                    }.bind(this)), s
                }
                return this.loadSectionFromState(t, !0)
            },
            sectionCache: function (t, i, e) {
                return "undefined" == typeof e ? this._cache[t + i] : void(this._cache[t + i] = e)
            },
            isDesktop: function () {
                return i(window).width() > 768
            },
            showRoot: function () {
                this.$section.html(""), this.displayState = "root", this.$root.find("h1").focus();
                var t = new i.Deferred;
                return t.resolve()
            },
            showSection: function (t) {
                t.title = this.getTitle(t.slug), this.setTitle(t.title), this.$section.html(t.sectionData.html), this.highlightSection("root", t.path), this.removeLoading(), this.updateBreadcrumbs(t);
                var e;
                "subsection" === this.displayState ? e = this.animateSubsectionToSectionDesktop() : "root" === this.displayState ? e = this.animateRootToSectionDesktop() : (e = new i.Deferred, e.resolve()), e.done(function () {
                    this.$section.find("h1").focus()
                }.bind(this))
            },
            animateSubsectionToSectionDesktop: function () {
                console.log('animateSubsectionToSectionDesktop');
                function t() {
                    this.displayState = "section", this.$el.removeClass("subsection").addClass("section"), this.$section.attr("style", ""), this.$section.find(".pane-inner").attr("style", ""), this.$section.addClass("with-sort"), this.$root.attr("style", ""), e.resolve()
                }
                var e = new i.Deferred;
                if (this.$root.css({
                        position: "absolute",
                        width: this.$root.width()
                    }), this.$subsection.hide(), this.$section.css("margin-right", "63%"), this.isDesktop()) {
                    this.$section.find(".pane-inner.curated").animate({
                        paddingLeft: "30px"
                    }, this.animateSpeed), this.$section.find(".pane-inner.alphabetical").animate({
                        paddingLeft: "96px"
                    }, this.animateSpeed);
                    var s = {
                        width: "35%",
                        marginLeft: "0%",
                        marginRight: "40%"
                    }
                } else var s = {
                    width: "30%",
                    marginLeft: "0%",
                    marginRight: "45%"
                };
                return this.$section.animate(s, this.animateSpeed, t.bind(this)), e
            },
            animateRootToSectionDesktop: function () {
                var t = new i.Deferred;
                return this.displayState = "section", this.$el.removeClass("subsection").addClass("section"), t.resolve()
            },
            showSubsection: function (t) {
                t.title = this.getTitle(t.slug), this.setTitle(t.title), this.$subsection.html(t.sectionData.html), this.highlightSection("section", t.path), this.highlightSection("root", "/browse/" + t.section), this.removeLoading(), this.updateBreadcrumbs(t);
                var e;
                "subsection" !== this.displayState ? e = this.animateSectionToSubsectionDesktop() : (e = new i.Deferred, e.resolve()), e.done(function () {
                    this.$subsection.find("h1").focus()
                }.bind(this))
            },
            animateSectionToSubsectionDesktop: function () {
                console.log('animateSectionToSubsectionDesktop');
                var t = new i.Deferred;
                if (this.$root.css({
                        position: "absolute",
                        width: this.$root.width()
                    }), this.$section.find(".sort-order").hide(), this.$section.find(".pane-inner").animate({
                        paddingLeft: "0"
                    }, this.animateSpeed), this.isDesktop()) var e = {
                    width: "25%",
                    marginLeft: "-13%",
                    marginRight: "63%"
                };
                else var e = {
                    width: "30%",
                    marginLeft: "-18%",
                    marginRight: "63%"
                };
                return this.$section.animate(e, this.animateSpeed, function () {
                    this.$el.removeClass("section").addClass("subsection"), this.$subsection.show(), this.$section.removeClass("with-sort"), this.displayState = "subsection", this.$section.find(".sort-order").attr("style", ""), this.$section.attr("style", ""), this.$section.find(".pane-inner").attr("style", ""), this.$root.attr("style", ""), t.resolve()
                }.bind(this)), t
            },
            getTitle: function (t) {
                var i = this.$el.find('a[href$="/servizi/miller_ajax/' + t + '"]:first'),
                    e = i.find("h3");
                return e.length > 0 ? e.text() : i.text()
            },
            setTitle: function (t) {
                i("title").text(t)
            },
            addLoading: function (t) {
                console.log(t);
                this.$el.attr("aria-busy", "true"), t.addClass("loading")
            },
            removeLoading: function () {
                this.$el.attr("aria-busy", "false"), this.$el.find("a.loading").removeClass("loading")
            },
            getSectionData: function (t) {
                var e = this.sectionCache("section", t.slug),
                    s = new i.Deferred,
                    //n = "/proto-MILLER/browse/" + t.slug + ".json";
                    //n = "http://MILLER.localhost/ajaxCalls.php?action=get_section_data&json=true";
                    //n= "http://sdc.local/app_dev.php/servizi/miller_ajax/" + t.slug;
                    n = window.location.origin  + t.path;
                //console.log(t);
                //console.log(n);

                return "undefined" != typeof t.sectionData ? s.resolve(t.sectionData) : "undefined" != typeof e ? s.resolve(e) : i.ajax({
                    url: n,
                    /*data: {
                        'slug': t.slug
                    },*/
                    dataType: 'json'
                }).done(function (i) {
                    this.sectionCache("section", t.slug, i), s.resolve(i)
                }.bind(this)), s
            },
            highlightSection: function (t, i) {
                this.$el.find("#" + t + " .active").removeClass("active"), this.$el.find('a[href$="' + i + '"]').parent().addClass("active")
            },
            parsePathname: function (t) {
                var i = {
                    path: t,
                    slug: t.replace(/(\/app_dev.php)?\/servizi\/miller_ajax\/?/, "")
                };
                return i.slug.indexOf("/") > -1 ? (i.section = i.slug.split("/")[0], i.subsection = i.slug.split("/")[1]) : i.section = i.slug, i
            },
            scrollToBrowse: function () {
                var t = i("body"),
                    e = this.$el.offset().top;
                t.scrollTop() > e && i("body").animate({
                    scrollTop: e
                }, this.animateSpeed)
            },
            loadSectionFromState: function (t, e) {
                var s = this.getSectionData(t);
                if (t.subsection) {
                    var n = s;
                    s = i.when(n)
                }
                return s.done(function (i) {
                    t.sectionData = i, this.scrollToBrowse(), t.subsection ? this.showSubsection(t) : this.showSection(t), "undefined" == typeof e && (history.pushState(t, "", t.path.replace('miller_ajax', 'miller'))/*, this.trackPageview(t)*/), this.lastState = t
                }.bind(this)), s
            },
            navigate: function (t) {
                if (t.currentTarget.pathname.match(/^(\/app_dev.php)?\/servizi\/miller_ajax\/[^\/]+(\/[^\/]+)?$/)) {
                    t.preventDefault();
                    var e = i(t.currentTarget),
                        s = this.parsePathname(t.currentTarget.pathname);
                    if (s.title = e.text(), s.path === window.location.pathname) return;
                    this.addLoading(e), this.loadSectionFromState(s)
                }
            },
            updateBreadcrumbs: function (t) {
                var e = this.$breadcrumbs.find("li");
                if (t.subsection) {
                    var s = t.section,
                        n = this.$section.find("h1").text();
                    if (1 === e.length) {
                        var o = i("<li />");
                        this.$breadcrumbs.append(o)
                    } else var o = e.slice(1);
                    o.html('<strong><a href="/servizi/miller/' + s + '">' + n + "</a></strong>")
                } else this.$breadcrumbs.find("li").slice(1).remove()
            },
            /*trackPageview: function (t) {
             var i = this.$section.find("h1").text();
             i = i ? i.toLowerCase() : "browse", MILLER.analytics && MILLER.analytics.trackPageview && MILLER.analytics.setSectionDimension && (MILLER.analytics.setSectionDimension(i), MILLER.analytics.trackPageview(t.path))
             }*/
        }, MILLER.BrowseColumns = t, i(function () {
            MILLER.browseColumns = new t({
                $el: i(".browse-panes")
            })
        })
    }();