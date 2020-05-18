<?php

namespace frontend\widgets\newWebPhone\contacts\helper;

use common\models\Client;
use common\models\Employee;

class ContactsHelper
{
    public static function processContactsList(array $contacts): array
    {
        $data = [];

        foreach ($contacts as $contact) {
            $contactData = [];
            $name = trim($contact['full_name']);
            $group = strtoupper($name[0] ?? 'A');
            $contactData['group'] = $group;
            $contactData['id'] = (int)$contact['id'];
            $contactData['name'] = $name;
            $contactData['description'] = $contact['description'] ?: '';
            $contactData['avatar'] = $group;
            $contactData['is_company'] = $contact['is_company'] ? true : false;
            $contactData['type'] = (int)$contact['type'];
            if ($contactData['type'] === Client::TYPE_INTERNAL) {
                $contactData['phones'] = array_keys(Employee::getPhoneList($contactData['id']));
                $contactData['emails'] = array_keys(Employee::getEmailList($contactData['id']));
            } else {
                if ($client = Client::findOne($contactData['id'])) {
                    $phones = [];
                    foreach ($client->clientPhones as $phone) {
                        $phones[] = $phone->phone;
                    }
                    $contactData['phones'] = $phones;
                    $emails = [];
                    foreach ($client->clientEmails as $email) {
                        $emails[] = $email->email;
                    }
                    $contactData['emails'] = $emails;
                } else {
                    \Yii::error('Not found client. Client Id: ' . $contactData['id'] . ' Type: ' . $contactData['type'], 'ContactsHelper');
                }
            }
            $data[$group][] = $contactData;
        }

        return $data;
    }

//    public static function processContacts(array $contacts): array
//    {
//        $contactsCollection = [];
//
//        foreach ($contacts as $contact) {
//            $index = 'id' . $contact['id'] . 'type' . $contact['type'];
//            if (isset($contactsCollection[$index])) {
//                if ($contact['phone']) {
//                    $contactsCollection[$index]['phones'][$contact['phone']] = $contact['phone'];
//                }
//                if ($contact['email']) {
//                    $contactsCollection[$index]['emails'][$contact['email']] = $contact['email'];
//                }
//                continue;
//            }
//            $contactData = [];
//            if ($contact['is_company']) {
//                $name = $contact['company_name'] ?: $contact['full_name'];
//            } else {
//                $name = $contact['full_name'];
//            }
//            $name = trim($name);
//            $group = strtoupper($name[0] ?? 'A');
//            $contactData['group'] = $group;
//            $contactData['id'] = $contact['id'];
//            $contactData['name'] = $name;
//            $contactData['description'] = $contact['description'] ?: '';
//            $contactData['avatar'] = $group;
//            $contactData['is_company'] = $contact['is_company'] ? true : false;
//            if (!$contact['phone']) {
//                $contactData['phones'] = [];
//            } else {
//                $contactData['phones'][$contact['phone']] = $contact['phone'];
//            }
//            if (!$contact['email']) {
//                $contactData['emails'] = [];
//            } else {
//                $contactData['emails'][$contact['email']] = $contact['email'];
//            }
//            $contactsCollection[$index] = $contactData;
//        }
//
//        $data = [];
//        foreach ($contactsCollection as $item) {
//            $item['phones'] = array_keys($item['phones']);
//            $item['emails'] = array_keys($item['emails']);
//            $group = $item['group'];
//            unset($item['group']);
//            $data[$group][] = $item;
//        }
//
//        ksort($data);
//
//        return $data;
//    }
}
