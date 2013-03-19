 function DispTimings() {
            $('#inline').css('display', '');
           
            html = $('#inline').html();
               $('#inline').modal();          
        }
		
    function SetTimings() {
            var bs = '08:00:00';
            var be = '11:00:00';
            var ls = '12:00:00';
            var le = '16:00:00';
            var ds = '19:00:00';
            var de = '23:00:00';

            var timings = "";

            //            $('.modalbox').fancybox({
            //                'width': 730,
            //                'height': 800
            //            });

            var clickFunction = ((document.ontouchstart !== null) ? 'onclick' : 'ontouchstart');
            var count = 0;
            var tim = "";
            //BREAKFAST TIME DISPLAY
            if (bs != null) {
                timings += "<table cellspacing='4' cellpadding='4' border='1' width='730px' >";
                timings += "<tr><td align='center' colspan='10' style='background-color:gray;'>Breakfast</td></tr>";

                var bst = new Date('1900/1/1 ' + bs);

                var bet = new Date('1900/1/1 ' + be);

                count = 0;
                timings += "<tr>";

                d1 = new Date(bst);
                d1.setMinutes(d1.getMinutes() - 30);
                for (t = 0; 1 == 1; t + 30) {
                    d2 = new Date(d1);
                    d2.setMinutes(d1.getMinutes() + 30);
                    tim = dateFormat(d2, "shortTime", false)
                    d1 = new Date(d2);

                    if (d1 > bet)
                        break;

                    if (count % 10 == 0)
                        timings += "</tr><tr>";

                    count++;
                    timings += "<td class='intable' " + clickFunction + "='SetTime($(this).html())'>" + tim + "</td>";

                }
                timings += "</tr></table>";

            }
            if (timings != "")
                timings += "<br/>";

            //LUNCH TIME DISPLAY
            if (ls != null) {
                timings += "<table cellspacing='4' cellpadding='4' border='1' width='730px' >";
                timings += "<tr><td align='center' colspan='10' style='background-color:gray;'>Lunch</td></tr>";

                var lst = new Date('1900/1/1 ' + ls);

                var let = new Date('1900/1/1 ' + le);

                count = 0;
                timings += "<tr>";

                d1 = new Date(lst);
                d1.setMinutes(d1.getMinutes() - 30);
                for (t = 0; 1 == 1; t + 30) {
                    d2 = new Date(d1);
                    d2.setMinutes(d1.getMinutes() + 30);
                    tim = dateFormat(d2, "shortTime", false)
                    d1 = new Date(d2);

                    if (d1 > let)
                        break;

                    if (count % 10 == 0)
                        timings += "</tr><tr>";

                    count++;
                    timings += "<td class='intable' " + clickFunction + "='SetTime($(this).html())'>" + tim + "</td>";

                }
                timings += "</tr></table>";

            }

            if (timings != "")
                timings += "<br/>";
            //DINNER TIME DISPLAY
            if (ds != null) {
                timings += "<table cellspacing='4' cellpadding='4' border='1' width='730px' >";
                timings += "<tr><td align='center' colspan='10' style='background-color:gray;'>Dinner</td></tr>";

                var dst = new Date('1900/1/1 ' + ds);

                var det = new Date('1900/1/1 ' + de);
                endHr = det.getHours();

                if (endHr == 0)
                    det = new Date('1900/1/2 ' + de);

                count = 0;
                timings += "<tr>";

                d1 = new Date(dst);

                d1.setMinutes(d1.getMinutes() - 30);
                for (t = 0; 1 == 1; t + 30) {
                    d2 = new Date(d1);
                    d2.setMinutes(d1.getMinutes() + 30);
                    tim = dateFormat(d2, "shortTime", false)
                    d1 = new Date(d2);

                    if (d1 > det)
                        break;

                    if (count % 10 == 0)
                        timings += "</tr><tr>";

                    count++;
                    timings += "<td class='intable' " + clickFunction + "='SetTime($(this).html())'>" + tim + "</td>";

                }
                timings += "</tr></table>";

            }

            if (timings != "")
                timings += "<br/>";

            $('#inline').html(timings);



        }
		
