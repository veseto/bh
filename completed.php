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
                $q = "select distinct playedMatches.seriesId
                      from playedMatches left join (matches, series, leagueDetails) 
                      on (playedMatches.matchId=matches.matchId and series.seriesId=playedMatches.seriesId and matches.leagueId=leagueDetails.leagueId) 
                      where userId=".$_SESSION['uid']." and active=0 and (bet>0 or betSoFar>0) order by matchDate desc, series.length desc, matches.leagueId asc, series.team asc";
                $res = $mysqli->query($q);
                $i = 1;
                while ($s = $res->fetch_assoc()) {
                ?>
                <tr>
                  <td><?=$i ?></td>
                  <td><?=$s['matchDate'] ?></td>
                  <td><img src="img/<?=$s['country'] ?>.png"> <?=$s['displayName'] ?></td>
                  <td><?=$s['team'] ?></td>
                  <td><?=$s['income'] ?> €</td>
                  <td><?=$s['profit'] ?> €</td>
                  <td><a href="seriesdetails.php?series=<?=$s['seriesId'] ?>">View</strong></a><br></td>
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