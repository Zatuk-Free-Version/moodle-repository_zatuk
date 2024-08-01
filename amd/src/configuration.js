// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines zatuk configuration script.
 *
 * @since      Moodle 2.0
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import ModalFactory from 'core/modal_factory';
import Ajax from 'core/ajax';
import {get_string as getString} from 'core/str';

const Selectors = {
    actions: {
        zatuksettings: '[data-action="zatuksettings"]',
        zatukplans: '[data-action="zatukplans"]',
        zatukdetails: '[data-action="zatukdetails"]',
        updatesettings: '[data-action="updatesettings"]',

    },
};
export const confirmbox = (message) => {
     ModalFactory.create({
        body: message,
        type: ModalFactory.types.ALERT,
        buttons: {
            ok: getString('Thank_you'),
        },
        removeOnClose: true,
      })
      .done(function(modal) {
        modal.show();
      });
};
export const init = () => {
    document.addEventListener('click', function(e) {
        let zatuksettings = e.target.closest(Selectors.actions.zatuksettings);
        if (zatuksettings) {
            e.preventDefault();
            $('.section_container').addClass('d-none');
            $('.section_container.registration_plans').removeClass('d-none');
            $('.step-1').addClass('completed');
            $('.step-2').addClass('active');
        }
    });
    document.addEventListener('click', function(e) {
        let zatukplans = e.target.closest(Selectors.actions.zatukplans);
        if (zatukplans) {
            e.preventDefault();
            var params = {};
            var organization = $("#id_organization").val();
            var zatuk_api_url = $("#id_zatuk_api_url").val();
            var organisationcode = $("#id_organization_code").val();
            var name = $("#id_name").val();
            var email = $("#id_email").val();
            var params = {};
            params.organization = organization;
            params.zatuk_api_url = zatuk_api_url;
            params.organisationcode = organisationcode;
            params.name = name;
            params.email = email;
            var promise = Ajax.call([{
                methodname: 'repository_zatukplans',
                args: params
            }]);
            promise[0].done(function(resp) {
                if(resp.success) {
                    getString('freetrailmessage' ,'repository_zatuk').then((str) => {
                        confirmbox(getString('finaltrailmessage','repository_zatuk',str));
                    });
                    $(".secret_keys").load(location.href + " .secret_keys");
                    $('.section_container').addClass('d-none');
                    $('.section_container.registration_keys').removeClass('d-none');
                    $('.step-2').addClass('completed');
                    $('.step-3').addClass('active');
                } else {
                    if(resp.errormessage === null && resp.success === null) {
                        confirmbox(getString('serverdown', 'repository_zatuk'));
                    } else {
                        confirmbox(resp.errormessage);
                    }
                }
            }).fail(function() {
                confirmbox(getString('errormessage', 'repository_zatuk'));
            });
        }
    });
    document.addEventListener('click', function(e) {
        let zatukdetails = e.target.closest(Selectors.actions.zatukdetails);
        if (zatukdetails) {
            e.preventDefault();
            var params = {};
            params.value = null;
            var promise = Ajax.call([{
                methodname: 'repository_enable_zatuk',
                args: params
            }]);
            promise[0].done(function() {
                location.reload();
            }).fail(function() {
                confirmbox(getString('exception'));
            });
        }
    });
    document.addEventListener('click', function(e) {
        let updatesettings = e.target.closest(Selectors.actions.updatesettings);
        if (updatesettings) {
            e.preventDefault();
            var params = {};
            var organization = $("#id_organization").val();
            var name = $("#id_name").val();
            var email = $("#id_email").val();
            var params = {};
            params.organization = organization;
            params.name = name;
            params.email = email;
            var promise = Ajax.call([{
                methodname: 'repository_updatezatuksettings',
                args: params
            }]);
            promise[0].done(function(resp) {
                if(resp) {
                    getString('updatemessage' ,'repository_zatuk').then((str) => {
                        confirmbox(getString('finaltrailmessage','repository_zatuk',str));
                    });
                }
            }).fail(function() {
                confirmbox('exception');
            });
        }
    });
};


