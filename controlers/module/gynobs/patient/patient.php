<?php
/*
 * This file is part of MedShakeEHR.
 *
 * Copyright (c) 2017
 * Bertrand Boutillier <b.boutillier@gmail.com>
 * http://www.medshake.net
 *
 * MedShakeEHR is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * MedShakeEHR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MedShakeEHR.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Module > Patient : la page du dossier patient
 * Complément Module Gynéco Obstétrique
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

// liste des formulaires fixes au 1er affichage dossier patient pour JS
$p['page']['listeForms']=array('gynObsATCD','gynObsSyntheseGyn');

// le formulaire latéral ATCD
$formLat = new msForm();
$p['page']['formNameGynObsATCD']=$formLat->setFormIDbyName('gynObsATCD');
$formLat->getPrevaluesForPatient($match['params']['patient']);
$p['page']['formLat']=$formLat->getForm();

// si LAP activé : allergie et atcd structurés
if($p['config']['LapOnOff'] == 'on') {

    // gestion atcd structurés
    $listeChampsAtcd=array('atcdObs','atcdPersoGyneco','atcdMedicChir');
    $gethtml=new msGetHtml;
    $gethtml->set_template('inc-patientAtcdStruc');
    foreach($listeChampsAtcd as $v) {
      $p['page']['beforeVar'][$v]=$patient->getAtcdStruc($v);
      if(empty($p['page']['beforeVar'][$v])) $p['page']['beforeVar'][$v]=array('fake');
      $p['page']['formLat']['before'][$v]=$gethtml->genererHtmlString($p['page']['beforeVar'][$v]);
    }
    unset($p['page']['beforeVar'], $listeChampsAtcd, $gethtml);

    // gestion allergies structurées
    $listeChampsAllergie=array('allergies');
    $gethtml=new msGetHtml;
    $gethtml->set_template('inc-patientAllergies');
    foreach($listeChampsAllergie as $v) {
      $p['page']['beforeVar'][$v]=$patient->getAllergies($v);
      if(empty($p['page']['beforeVar'][$v])) $p['page']['beforeVar'][$v]=array('fake');
      $p['page']['formLat']['before'][$v]=$gethtml->genererHtmlString($p['page']['beforeVar'][$v]);
    }
    unset($p['page']['beforeVar'], $listeChampsAllergie, $gethtml);
}

//formulaire synthèse de gynéco
$formSynthese = new msForm();
$p['page']['formNameGynObsSyntheseGyn']=$formSynthese->setFormIDbyName('gynObsSyntheseGyn');
$formSynthese->getPrevaluesForPatient($match['params']['patient']);
$p['page']['formSynthese']=$formSynthese->getForm();


//types de consultation liées à la gynéco classique.
$typeCsCla=new msData;
$p['page']['typeCsCla']=$typeCsCla->getDataTypesFromCatName('csGyneco', array('id','label', 'formValues'));


//chercher une grossesse en cours (cad si pas de type 245 associé)
$name2typeID = $typeCsCla->getTypeIDsFromName(['groFermetureSuivi', 'nouvelleGrossesse']);
if ($findGro=msSQL::sqlUnique("select pd.id as idGro, eg.id as idFin
  from objets_data as pd
  left join objets_data as eg on pd.id=eg.instance and eg.typeID='".$name2typeID['groFermetureSuivi']."' and eg.outdated='' and eg.deleted=''
  where pd.toID='".$p['page']['patient']['id']."' and pd.typeID='".$name2typeID['nouvelleGrossesse']."' and pd.outdated='' and pd.deleted='' order by pd.creationDate desc
  limit 1")) {
    if (!$findGro['idFin']) {
        $p['page']['grossesseEnCours']['id']=$findGro['idGro'];

        // générer le formulaire grossesse tête de page.
        $formSyntheseGrossesse = new msForm();
        $p['page']['formNameGynObsSyntheseObs']=$formSyntheseGrossesse->setFormIDbyName('gynObsSyntheseObs');
        $formSyntheseGrossesse->setInstance($p['page']['grossesseEnCours']['id']);
        $formSyntheseGrossesse->getPrevaluesForPatient($match['params']['patient']);
        $p['page']['formSyntheseGrossesse']=$formSyntheseGrossesse->getForm();

        // complément à la liste des formulaires fixes au 1er affichage dossier patient pour JS
        $p['page']['listeForms'][]='gynObsSyntheseObs';

        //types de consultation liées à la grossesse.
        $typeCsGro=new msData;
        $p['page']['typeCsGro']=$typeCsGro->getDataTypesFromCatName('csObs', array('id','label','formValues'));
    }
}

//fixer les paramètres pour les formulaires d'ordonnance et de règlement du module
$p['page']['formReglement']['reglePorteur']=array('module'=>'gynobs', 'form'=>'baseReglement');
$p['page']['formOrdo']['ordoPorteur']=array('module'=>'base', 'form'=>'');
