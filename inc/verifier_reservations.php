<?php
    if (!defined("_ECRIRE_INC_VERSION")) return;
    
    function inc_verifier_reservations_dist($objet,$id_objet,$date_debut,$date_fin,$id_location){
        
        echo $id_location;
        
        $where=array(
        'objet='.sql_quote($objet),
        'id_objet='.$id_objet,
        '(date_debut<='.sql_quote($date_fin).'OR date_fin<='.sql_quote($date_debut).')',
        );
        if(intval($id_location))$where[]='id_location!='.$id_location;
        
        echo serialize($where);
        $sql=sql_select('date_debut,date_fin,id_location','spip_locations',$where);
        
        /*if                    $retour=0;
                    // date_debut et date_fin à l'interieur sachant que date_debut peut etre egale orr_date_fin et que date_fin peut etre égale à orr_date_debut
                    if (($r[orr_date_debut]<=$date_debut) and ($r[orr_date_fin]>=$date_fin)){
                        $retour=1;
                        break;
                    }
                    //~ date_debut < à orr_date_debut et date_fin > orr_date_debut
                    if (($r[orr_date_debut]>=$date_debut) and ($r[orr_date_debut]<$date_fin)){
                        $retour=1;
                        break;
                    }
                    // date_fin > date de fin et que ma date debut < date de fin
                    if (($r[orr_date_fin]>$date_debut) and ($r[orr_date_fin]<$date_fin)) {
                        $retour=1;
                        break;
                    }*/
        
        
        while($data=sql_fetch($sql)){
            
            echo '<div>'.$data['id_location'].' debut'.$data['date_debut'].'- fin'.$data['date_fin'].'</div>';       
            
        }

        
        return sql_count($sql);
    }
