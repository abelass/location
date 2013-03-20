<?php
    if (!defined("_ECRIRE_INC_VERSION")) return;
    
    function inc_verifier_reservations_dist($objet,$id_objet,$date_debut,$date_fin,$id_location){

        $where=array(
        'objet='.sql_quote($objet),
        'id_objet='.$id_objet,
        'date_fin>='.sql_quote($date_debut),
        );
        if(intval($id_location))$where[]='id_location!='.$id_location;

        $sql=sql_select('date_debut,date_fin,id_location','spip_locations',$where,'','date_debut');
        
        
        $erreurs=array();
        $date_debut_limite=array();
        while($data=sql_fetch($sql)){
           //La date début séléctionné doit être supérieur
            if(
            ($data['date_debut']>=$date_debut AND $data['date_fin']<=$date_debut) OR
            ($data['date_debut']<=$date_debut AND $data['date_fin']<=$date_fin)
           ){
               $erreurs['date_debut']=_T('location:erreur_date_debut_complet',array('date_debut'=>$data['date_fin']));
               set_request('date_debut',$data['date_fin']);
            }
            elseif($data['date_fin']<=$date_debut AND $data['date_fin']>=$date_fin){
                $erreurs['date_debut']=_T('location:erreur_date_debut_complet',array('date_debut'=>$data['date_debut']));
                set_request('date_debut',$data['date_debut']);
               break;
            }
            elseif(
            ($data['date_debut']<=$date_fin AND $data['date_fin']<=$date_debut) OR 
            ($data['date_fin']>=$date_fin AND $data['date_debut']<=$date_debut )){
                $erreurs['date_fin']=_T('location:erreur_date_fin_complet',array('date_fin'=>$data['date_debut']));
            }
            elseif(
            ($data['date_debut']>=$date_debut AND $data['date_fin']<=$date_fin) OR 
            ($data['date_debut']>=$date_debut AND $data['date_debut']<=$date_fin) OR
            ($data['date_debut']<=$date_debut AND $data['date_fin']<=$date_fin)) {
                if($date_debut<$date_fin)$erreurs['date_fin']=_T('location:erreur_date_fin_complet',array('date_fin'=>$data['date_debut'])); 
                else  {
                     $erreurs['date_debut']=_T('location:erreur_date_pas possible');
                     $erreurs['date_fin']=_T('location:erreur_date_pas possible');
                     return $erreurs;
                }
               set_request('date_fin',$data['date_debut']);
               break;
            }
        }

    if(count($erreurs)>1){
        $erreurs['date_debut']=_T('location:erreur_date_complet');
        $erreurs['date_fin']=_T('location:erreur_date_complet');
        }
        return $erreurs;
    }

