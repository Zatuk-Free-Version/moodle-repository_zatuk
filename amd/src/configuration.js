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
 * @copyright  2023 Moodle India
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import Ajax from 'core/ajax';
import {get_string as getString} from 'core/str';
import messagemodal from 'repository_zatuk/messagemodal';
import Templates from 'core/templates';

const Selectors = {
    actions: {
        zatuksettings: '[data-action="zatuksettings"]',
        zatukplans: '[data-action="zatukplans"]',
        zatukdetails: '[data-action="zatukdetails"]',
        updatesettings: '[data-action="updatesettings"]',

    },
};

let MessageModal = new messagemodal();

export const init = () => {
    document.addEventListener('click', function(e) {
        let zatuksettings = e.target.closest(Selectors.actions.zatuksettings);
        if (zatuksettings) {
            let organization = $("#id_organization").val();
            let organizationcode = $("#id_organization_code").val();
            let name = $("#id_name").val();
            let email = $("#id_email").val();
            e.preventDefault();
            if(organization === '' || organizationcode === '' || name === '' ||  email === '' ) {
                getString('requiredallfields' ,'repository_zatuk').then((str) => {
                    MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                });
            } else {
                if (isValidEmail(email)) {
                    $.ajax({
                        method: "GET",
                        dataType: "json",
                        url: M.cfg.wwwroot + "/repository/zatuk/ajax.php?organization="+organization+
                        "&organizationcode="+organizationcode+
                        "&name="+name+
                        "&email="+email+"",
                        success: function(){
                            $('.section_container').addClass('d-none');
                            $('.section_container.registration_plans').removeClass('d-none');
                            $('.step-1').addClass('completed');
                            $('.step-2').addClass('active');
                        }
                    });

                } else {
                    getString('validemailidrequired', 'repository_zatuk', email).then((str) => {
                        MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                    });
                }

            }
        }
    });
    document.addEventListener('click', function(e) {
        let zatukplans = e.target.closest(Selectors.actions.zatukplans);
        if (zatukplans) {
            e.preventDefault();
            Templates.render('repository_zatuk/loader', {}).then(function(html, js) {
              Templates.appendNodeContents('#page-content', html, js);
            });
            var organization = $("#id_organization").val();
            var organizationcode = $("#id_organization_code").val();
            var name = $("#id_name").val();
            var email = $("#id_email").val();
            var params = {};
            params.organization = organization;
            params.organizationcode = organizationcode;
            params.name = name;
            params.email = email;
            var promise = Ajax.call([{
                methodname: 'repository_configure_zatuk',
                args: params
            }]);
            promise[0].done(function(resp) {
                if(resp.success) {
                     var params = {};
                     params.haskeygenerated = resp.success;
                    var promise = Ajax.call([{
                        methodname: 'repository_enable_zatuk',
                        args : params
                    }]);
                    promise[0].done(function(enbresponse) {
                        if (enbresponse.success) {
                            $('.zatuk-page-loader').addClass('d-none');
                            getString('freetrailmessage' ,'repository_zatuk').then((str) => {
                                MessageModal.confirmbox(getString('finaltrailmessage','repository_zatuk',str));
                            });
                            $(".secret_keys").load(location.href + " .secret_keys");
                            $('.section_container').addClass('d-none');
                            $('.section_container.registration_keys').removeClass('d-none');
                            $('.step-2').addClass('completed');
                            $('.step-3').addClass('active');
                        } else {
                            $('.zatuk-page-loader').addClass('d-none');
                            getString('keysecretnotgenerated', 'repository_zatuk').then((str) => {
                               MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                            });
                        }
                    }).fail(function() {
                        $('.zatuk-page-loader').addClass('d-none');
                        getString('errormessage', 'repository_zatuk').then((str) => {
                           MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                        });
                    });

                } else {
                    if(resp.message.length > 2) {
                        $('.zatuk-page-loader').addClass('d-none');
                        MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',resp.message));

                    } else {
                        $('.zatuk-page-loader').addClass('d-none');
                        getString('serverdown' ,'repository_zatuk').then((str) => {
                          MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                        });
                    }
                }
            }).fail(function() {
                $('.zatuk-page-loader').addClass('d-none');
                getString('errormessage', 'repository_zatuk').then((str) => {
                  MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                });
            });
        }
    });
    document.addEventListener('click', function(e) {
        let zatukdetails = e.target.closest(Selectors.actions.zatukdetails);
        if (zatukdetails) {
            window.addEventListener('beforeunload', function (event) {
                event.stopImmediatePropagation();
            });
            window.location.reload();
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
            if(organization === '' || name === '' ||  email === '' ) {
                getString('requiredallfields' ,'repository_zatuk').then((str) => {
                    MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                });
            } else {
                if (isValidEmail(email)) {
                    var params = {};
                    params.organization = organization;
                    params.name = name;
                    params.email = email;
                    var promise = Ajax.call([{
                        methodname: 'repository_update_zatuk_settings',
                        args: params
                    }]);
                    promise[0].done(function(resp) {
                        if(resp.success) {
                            getString('updatemessage' ,'repository_zatuk').then((str) => {
                                MessageModal.confirmbox(getString('finaltrailmessage','repository_zatuk',str));
                            });
                        }
                    }).fail(function() {
                       getString('errormessage', 'repository_zatuk').then((str) => {
                            MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                        });
                    });

                } else {
                    getString('validemailidrequired', 'repository_zatuk', email).then((str) => {
                        MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                    });
                }

            }

        }
    });

    $(document).on('click','.stage-a', function(){

        $('.section_container').removeClass('d-none');

        $('.section_container.registration_plans').addClass('d-none');

        $('.section_container.registration_keys').addClass('d-none');

        $('.stage-form .section_container').addClass('active');

        $('.step-1').removeClass("completed");

        $('.step-2').removeClass('active');


    });

    $(document).on('click','.stage-b', function(){

        let organization = $("#id_organization").val();
        let organizationcode = $("#id_organization_code").val();
        let name = $("#id_name").val();
        let email = $("#id_email").val();

         if(organization === '' || organizationcode === '' || name === '' ||  email === '' ) {

            getString('requiredallfields' ,'repository_zatuk').then((str) => {
                MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
            });

        } else {

            if (isValidEmail(email)) {
                $('.section_container').addClass('d-none');
                $('.section_container.registration_plans').removeClass('d-none');
                $('.stage-form .section_container').removeClass('active');
                $('.stage-form .section_container').addClass('d-none');

                $('.step-1').addClass('completed');
                $('.step-2').addClass('active');

            } else {
                getString('validemailidrequired', 'repository_zatuk', email).then((str) => {
                    MessageModal.confirmbox(getString('failedwarningmessage','repository_zatuk',str));
                });
            }

        }

    });

    /**
     * Function to validate an email address.
     * @param {string} inputval - an email.
     */
    function isValidEmail(inputval) {
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return regex.test(inputval);
    }

};


