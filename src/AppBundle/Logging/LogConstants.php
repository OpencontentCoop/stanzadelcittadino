<?php
/**
 * @copyright Copyright (C) 2017 Opencontent Società Cooperativa,  All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package ocsdc
 */

namespace AppBundle\Logging;

/**
 * Class Constants
 * @package AppBundle\Logging
 */
class LogConstants
{
    const USER_HAS_TO_ACCEPT_TERMS = "user still hasn't accepted platform terms.";
    const USER_HAS_ACCEPTED_TERMS = "User has accepted the terms of service";
    const USER_HAS_CHANGED_CONTACTS_INFO = "User has changed the contact informations";

    const CPS_USER_CREATED = "A new {type} user has been created";
    const CPS_USER_CREATED_WITH_BOGUS_DATA = "User data has partially bogus data";

    const PRATICA_CREATED = "A new {type} pratica has been created";
    const PRATICA_UPDATED = "A new {id} pratica has been updated";

    const PRATICA_COMPILING_STEP = "User {user} view step {step} of pratica #{pratica}";
    const PRATICA_FASCICOLO_ASSEGNATO = "Pratica %s : Assigned Numero Fascicolo %s";
    const PRATICA_PROTOCOLLO_ASSEGNATO = "Pratica %s : Assigned Numero Protocollo %s";
    const ALLEGATO_DOWNLOAD_PERMESSO_CPSUSER = "CPS User {user} downloaded attachment {originalFilename} with id {pratica}";
    const ALLEGATO_DOWNLOAD_PERMESSO_OPERATORE = "Operatore {user} downloaded attachment {originalFilename} Id: {allegato} . He was allowed to because he's responsible for these pratiche : {pratiche}";
    const ALLEGATO_DOWNLOAD_NEGATO = "Denied download of Attachment {originalFilename} Id: {allegato} ";
    const ALLEGATO_CANCELLAZIONE_NEGATA = "Denied deleting allegato {allegato} by user {user}";
    const ALLEGATO_CANCELLAZIONE_PERMESSA = "Allowed delete of allegato {allegato} by user {user}";

    const PRATICA_CHANGED_STATUS = "Pratica {pratica} status has changed from {before_status} to {after_status}";
    const PRATICA_CHANGED_STATUS_FAILED = "Failed changing pratica {pratica} from {before_status} to {after_status}, error: {error}";

    const PRATICA_ASSIGNED = "Pratica {pratica} assigned to user {user}";
    const PRATICA_COMMENTED = "Pratica {pratica} commented by user {user}";
    const PRATICA_APPROVED = "Pratica {pratica} approved by user {user}";
    const PRATICA_CANCELLED = "Pratica {pratica} cancelled by user {user}";
    const PRATICA_WRONG_ENTE_REQUESTED = "Pratica {pratica} created, but wrong ente requested: {headers}";
    const PRATICA_APPROVED_WAIT_REGISTRATION = "Pratica {pratica} completed by user {user} and wait new registration";

    const OPERATORE_ADMIN_HAS_CHANGED_OPERATORE_AMBITO = "Operatore Admin {operatore_admin} has changed {operatore} ambito";
    const PRATICA_UPDATED_STATUS_FROM_GPA = "Pratica status has been updated from GPA {statusChange}";
    const PRATICA_ERROR_IN_UPDATED_STATUS_FROM_GPA = "Error in trying to update Pratica status from GPA {statusChange}, {error}";

    const PRATICA_CHANGE_STATUS = "Pratica change status handled by listener {listener}: new status is {status}";

    const PROTOCOLLO_SEND_ERROR = "Error {error_number} sending pratica {pratica} to protocollo";
    const PROTOCOLLO_UPLOAD_ERROR = "Error {error_number} uploading allegato {allegato} of pratica {pratica} to protocollo";
    const PROTOCOLLO_UPLOADOPERATORE_ERROR = "Error {error_number} uploading allegati operatore of pratica {pratica} to protocollo";


}
