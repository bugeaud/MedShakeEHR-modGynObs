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
 * Js pour le formulaire 21 issue de grossesse
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 */

$(document).ready(function() {

  //echos IG : observation nombre foetus
  $('body').on("keyup, change", '#p_244ID', function() {
    afficherFxNbFoetus();
  });

  //issue grossesse : calcul terme à l'acc
  $('body').on("focusout", '#p_225ID', function() {
    terme = termeAccCalc($('#p_225ID').val(), $('#p_45ID').val(), $('#p_49ID').val());
    if (terme != null) {
      $('#p_226ID, #p_232ID, #p_238ID').val(terme);
    }
  });

});
