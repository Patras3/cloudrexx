<?php

/**
 * Cloudrexx
 *
 * @link      http://www.cloudrexx.com
 * @copyright Cloudrexx AG 2007-2015
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Cloudrexx" is a registered trademark of Cloudrexx AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * RegistrationForm
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_calendar
*/
namespace Cx\Modules\Calendar\Model\Entity;

/**
 * RegistrationForm
 *
 * @SWG\Definition(definition="RegistrationForm", type= "object")
 * @SWG\Get(
 *     path="/calendar-registration-form",
 *     tags={"registrationForm"},
 *     summary="Lists Registration Forms",
 *     @SWG\Parameter(
 *         name="order",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="Order of Registration form"
 *     ),
 *     @SWG\Parameter(
 *         name="filter",
 *         in="query",
 *         type="string",
 *         required=false,
 *         description="filter by Registration form"
 *     ),
 *     @SWG\Parameter(
 *         name="limit",
 *         in="query",
 *         type="integer",
 *         format="int32",
 *         required=false,
 *         description="maximum number of results to return"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="A list of all registration form",
 *         @SWG\Schema(ref="#/definitions/apiResponse"),
 *         examples={
 *           "application/json": {
 *             "status" : "success",
 *             "message" : "Registration forms listed successfully",
 *             "meta" : {},
 *             "data" : {
 *               "RegistrationForms" : {
 *                 {
 *                   "id" : "1",
 *                   "status" : 1,
 *                   "order" : 5,
 *                   "title" : "Standardformular",
 *                   "events" : {},
 *                   "registrationFormFields" : {
 *                     {
 *                       "id" : 1,
 *                       "type" : "Saluation",
 *                       "required" : 1,
 *                       "order" : 2,
 *                       "affiliation" : "form",
 *                       "registrationFormFieldNames" : {
 *                         {
 *                           "fieldId" : 1,
 *                           "formId" : 1,
 *                           "langId" : 2,
 *                           "name" : "Salutation",
 *                           "default" : "Dear Ms.,Dear Mr."
 *                         }
 *                       },
 *                       "registrationFormFieldValues" : {
 *                         {
 *                           "regId" : 1,
 *                           "fieldId" : 1,
 *                           "value" : "Dear Ms.",
 *                           "registration" : {}
 *                         }
 *                       }
 *                     }
 *                   }
 *                 }
 *               }
 *             }
 *           }
 *         }
 *     )
 * )
 * @SWG\Get(
 *     path="/calendar-registration-form/{id}",
 *     tags={"registration form"},
 *     summary="Fetch a registration form",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the registration form"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration form description",
 *         @SWG\Schema(ref="#/definitions/apiResponse"),
 *         examples={
 *           "application/json": {
 *             "status" : "success",
 *             "message" : "Registration form listed successfully",
 *             "meta" : {},
 *             "data" : {
 *               "RegistrationForm" : {
 *                 "id" : "1",
 *                 "status" : 1,
 *                 "order" : 5,
 *                 "title" : "Standardformular",
 *                 "events" : {},
 *                 "registrationFormFields" : {
 *                   {
 *                     "id" : 1,
 *                     "type" : "Saluation",
 *                     "required" : 1,
 *                     "order" : 2,
 *                     "affiliation" : "form",
 *                     "registrationFormFieldNames" : {
 *                       {
 *                         "fieldId" : 1,
 *                         "formId" : 1,
 *                         "langId" : 2,
 *                         "name" : "Salutation",
 *                         "default" : "Dear Ms.,Dear Mr."
 *                       }
 *                     },
 *                     "registrationFormFieldValues" : {
 *                       {
 *                         "regId" : 1,
 *                         "fieldId" : 1,
 *                         "value" : "Dear Ms.",
 *                         "registration" : {
 *                           "id" : "200",
 *                           "date" : "1493813018",
 *                           "hostName" : "www.example.com",
 *                           "ipAddress" : "192.165.3.87",
 *                           "type" : 1,
 *                           "key" : "478roF24hkH248854429F1",
 *                           "userId" : 2,
 *                           "langId" : 1,
 *                           "export" : 1311860736,
 *                           "paymentMethod" : 2,
 *                           "paid" : 1,
 *                           "registrationFormFieldValues" : {
 *                             {
 *                               "regId" : 200,
 *                               "fieldId" : 1,
 *                               "value" : "Dear Mr."
 *                             },
 *                             {
 *                               "regId" : 200,
 *                               "fieldId" : 2,
 *                               "value" : "David warner"
 *                             }
 *                           },
 *                           "event" : {}
 *                         }
 *                       }
 *                     }
 *                   }
 *                 }
 *               }
 *             },
 *           }
 *         }
 *     )
 * )
 * @SWG\Post(
 *     path="/calendar-registration-form",
 *     tags={"registration form"},
 *     summary="Create a new Registration form",
 *     @SWG\Parameter(
 *         name="registrationForm",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/RegistrationForm")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration form added",
 *         @SWG\Schema(ref="#/definitions/apiResponse"),
 *         examples={
 *           "application/json": {
 *             "status" : "success",
 *             "message" : "Registration form added successfully",
 *             "meta" : {},
 *             "data" : {
 *               "RegistrationForm" : {
 *                 "id" : "1",
 *                 "status" : 1,
 *                 "order" : 3,
 *                 "title" : "new Standardformular",
 *                 "events" : {},
 *                 "registrationFormFields" : {
 *                   {
 *                     "id" : 2,
 *                     "type" : "firstname",
 *                     "required" : 1,
 *                     "order" : 3,
 *                     "affiliation" : "form",
 *                     "registrationFormFieldNames" : {
 *                       {
 *                         "fieldId" : 2,
 *                         "formId" : 2,
 *                         "langId" : 2,
 *                         "name" : "firstname",
 *                         "default" : ""
 *                       }
 *                     },
 *                     "registrationFormFieldValues" : {
 *                       {
 *                         "regId" : 2,
 *                         "fieldId" : 2,
 *                         "value" : "David",
 *                         "registration" : {
 *                           "id" : "200",
 *                           "date" : "1493813018",
 *                           "hostName" : "www.example.com",
 *                           "ipAddress" : "192.165.3.87",
 *                           "type" : 1,
 *                           "key" : "478roF24hkH248854429F1",
 *                           "userId" : 2,
 *                           "langId" : 1,
 *                           "export" : 1311860736,
 *                           "paymentMethod" : 2,
 *                           "paid" : 1,
 *                           "registrationFormFieldValues" : {
 *                             {
 *                               "regId" : 200,
 *                               "fieldId" : 1,
 *                               "value" : "Dear Mr."
 *                             },
 *                             {
 *                               "regId" : 200,
 *                               "fieldId" : 2,
 *                               "value" : "David warner"
 *                             }
 *                           },
 *                           "event" : {}
 *                         }
 *                       }
 *                     }
 *                   }
 *                 }
 *               }
 *             }
 *           }
 *         }
 *     )
 * )
 * @SWG\Put(
 *     path="/calendar-registration-form/{id}",
 *     tags={"registrationForm"},
 *     summary="Update a Registration form",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the registration form"
 *     ),
 *     @SWG\Parameter(
 *         name="registrationForm",
 *         in="body",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/RegistrationForm")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration form updated",
 *         @SWG\Schema(ref="#/definitions/apiResponse"),
 *         examples={
 *           "application/json": {
 *             "status" : "success",
 *             "message" : "Registration form added successfully",
 *             "meta" : {},
 *             "data" : {
 *               "RegistrationForm" : {
 *                 "id" : "2",
 *                 "status" : 1,
 *                 "order" : 6,
 *                 "title" : "new Standardformular",
 *                 "events" : {},
 *                 "registrationFormFields" : {
 *                   {
 *                     "id" : 2,
 *                     "type" : "firstname",
 *                     "required" : 1,
 *                     "order" : 6,
 *                     "affiliation" : "form",
 *                     "registrationFormFieldNames" : {
 *                       {
 *                         "fieldId" : 2,
 *                         "formId" : 2,
 *                         "langId" : 2,
 *                         "name" : "firstname",
 *                         "default" : ""
 *                       }
 *                     },
 *                     "registrationFormFieldValues" : {
 *                       {
 *                         "regId" : 2,
 *                         "fieldId" : 2,
 *                         "value" : "David warner",
 *                         "registration" : {
 *                           "id" : "200",
 *                           "date" : "1493813018",
 *                           "hostName" : "www.example.com",
 *                           "ipAddress" : "192.165.3.87",
 *                           "type" : 1,
 *                           "key" : "478roF24hkH248854429F1",
 *                           "userId" : 2,
 *                           "langId" : 1,
 *                           "export" : 1311860736,
 *                           "paymentMethod" : 2,
 *                           "paid" : 1,
 *                           "registrationFormFieldValues" : {
 *                             {
 *                               "regId" : 200,
 *                               "fieldId" : 1,
 *                               "value" : "Dear Mr."
 *                             },
 *                             {
 *                               "regId" : 200,
 *                               "fieldId" : 2,
 *                               "value" : "David warner"
 *                             }
 *                           },
 *                           "event" : {}
 *                         }
 *                       }
 *                     }
 *                   }
 *                 }
 *               }
 *             },
 *           }
 *         }
 *     )
 * )
 * @SWG\Delete(
 *     path="/calendar-registration-form/{id}",
 *     tags={"registrationForm"},
 *     summary="Delete a Registration form",
 *     @SWG\Parameter(
 *         name="id",
 *         in="path",
 *         type="string",
 *         required=true,
 *         description="ID of the registration form"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Registration form deleted",
 *         @SWG\Schema(
 *             ref="#/definitions/apiResponse"
 *         )
 *     )
 * )
 *
 * @copyright   Cloudrexx AG
 * @author      Project Team SS4U <info@cloudrexx.com>
 * @package     cloudrexx
 * @subpackage  module_calendar
*/
class RegistrationForm extends \Cx\Model\Base\EntityBase {
    /**
     * @SWG\Property(
     *     type="integer",
     *     format="int64",
     *     description="Unique identifier representing a specific registration"
     * )
     *
     * @var integer $id
     */
    protected $id;

    /**
     * @SWG\Property(type="boolean")
     *
     * @var integer $status
     */
    protected $status;

    /**
     * @SWG\Property(type="integer", format="int32")
     *
     * @var integer $order
     */
    protected $order;

    /**
     * @SWG\Property(type="string")
     *
     * @var string $title
     */
    protected $title;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref="#/definitions/Event")
     * )
     *
     * @var Cx\Modules\Calendar\Model\Entity\Event
     */
    protected $events;

    /**
     * @SWG\Property(
     *     type="array",
     *     @SWG\Items(ref="#/definitions/RegistrationFormField")
     * )
     *
     * @var Cx\Modules\Calendar\Model\Entity\RegistrationFormField
     */
    protected $registrationFormFields;

    public function __construct()
    {
        $this->status = 0;
        $this->order  = 99;
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->registrationFormFields = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set order
     *
     * @param integer $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get order
     *
     * @return integer $order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add event
     *
     * @param Cx\Modules\Calendar\Model\Entity\Event $event
     */
    public function addEvent(\Cx\Modules\Calendar\Model\Entity\Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * set events
     *
     * @param Doctrine\Common\Collections\Collection $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }

    /**
     * Get events
     *
     * @return Doctrine\Common\Collections\Collection $events
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add registrationFormField
     *
     * @param Cx\Modules\Calendar\Model\Entity\RegistrationFormField $registrationFormField
     */
    public function addRegistrationFormField(\Cx\Modules\Calendar\Model\Entity\RegistrationFormField $registrationFormField)
    {
        $this->registrationFormFields[] = $registrationFormField;
    }

    /**
     * Get RegistrationFormFieldById
     *
     * @param integer $id id
     *
     * @return null|\Cx\Modules\Calendar\Model\Entity\RegistrationFormField
     */
    public function getRegistrationFormFieldById($id)
    {
        if (!$id) {
            return null;
        }

        foreach ($this->registrationFormFields as $formField) {
            if ($formField->getId() == $id) {
                return $formField;
            }
        }
        return null;
    }

    /**
     * Set RegistrationFormFields
     *
     * @param Doctrine\Common\Collections\Collection $registrationFormFields
     */
    public function setRegistrationFormFields($registrationFormFields) {
        $this->registrationFormFields = $registrationFormFields;
    }

    /**
     * Get registrationFormFields
     *
     * @return Doctrine\Common\Collections\Collection $registrationFormFields
     */
    public function getRegistrationFormFields()
    {
        return $this->registrationFormFields;
    }
}