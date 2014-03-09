<div class="panel panel-default">
          <div class="panel-body">
            <table class="table table-bordered table-condensed text-center noPaddingMargin" id="currentSeries">
              <thead>
                <tr>
                  <th><input type="hidden"></th>
                  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="date"></th>
                  <th><input type="text" name="search_engine" class="search_init shortInput" placeholder="league"></th>
                  <th><input type="text" name="search_engine" class="search_init " placeholder="team"></th>
                  <th><input type="hidden"></th>
                  <th><input type="hidden"></th>
                  <th><input type="hidden"></th>
                </tr>
                <tr>
                  <th>#</th>
                  <th>Date</th>
                  <th>League</th>
                  <th>Team</th>
                  <th>Income</th>
                  <th>Profit</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $q = "select *
                      from playedMatches left join (matches, series, leagueDetails) 
                      on (playedMatches.matchId=matches.matchId and series.seriesId=playedMatches.seriesId and matches.leagueId=leagueDetails.leagueId) 
                      where userId=".$_SESSION['uid']." and (bet>0 or betSoFar>0) and resultShort='D' order by matchDate desc, matchTime desc";
                $res = $mysqli->query($q);
                
                $i = 1;
                while ($match = $res->fetch_array()) {
                    // $res2 = $mysqli -> query("SELECT * FROM playedMatches 
                    //     left join (matches, leagueDetails, series) 
                    //     on (matches.matchId=playedMatches.matchId and leagueDetails.leagueId=matches.leagueId and series.seriesId=playedMatches.seriesId)
                    //     where playedMatches.seriesId=".$s[0]." and userId=".$_SESSION['uid']." and resultShort='D' and (betSoFar>0 or bet>0) order by matchDate desc");
                    // echo $mysqli->error;
                    // $match = $res2->fetch_assoc();
                ?>
                <tr>
                  <td><?=$i ?></td>
                  <td><?=$match['matchDate'] ?></td>
                  <td><img src="img/<?=$match['country'] ?>.png"> <?=$match['displayName'] ?></td>
                  <td><?=$match['team'] ?></td>
                  <td><?=$match['income'] ?></td>
                  <td <?php if ($match['profit'] < 0) echo "class='livescore1HF'";?>><?=$match['profit'] ?></td>
                  <td><a href="seriesdetails.php?series=<?=$match['seriesId'] ?>">View</strong></a><br></td>
                </tr>
                <?php 
                  $i ++;
                  }
                ?>
              </tbody>
            </table>
          </div>
         </div> <!-- // series --> 
        <script type="text/javascript">
    var asInitVals = new Array();
   
    $(document).ready(function() {  
      /* Init DataTables */
        var oTable = $('#currentSeries').dataTable({
          "iDisplayLength": 25,
          "bJQueryUI": true,
          "sPaginationType": "full_numbers",
          "sDom": '<"top" lf><"toolbar">irpti<"bottom"pT><"clear">',
          "oTableTools": {
            "sSwfPath": "dt/media/swf/copy_csv_xls_pdf.swf"
          }
      });

      $("thead input").keyup( function () {
      /* Filter on the column (the index) of this element */
        oTable.fnFilter( this.value, $("thead input").index(this));
      } );
      
      /*
       * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
       * the footer
       */
      $("thead input").each( function (i) {
        asInitVals[i] = this.value;
      } );
      
      $("thead input").focus( function () {
        if ( this.className == "search_init" )
        {
          this.className = "";
          this.value = "";
        }
      } );
      
      $("thead input").blur( function (i) {
        if ( this.value == "" )
        {
          this.className = "search_init";
          this.value = asInitVals[$("thead input").index(this)];
        }
      } );
    } );
      </script>