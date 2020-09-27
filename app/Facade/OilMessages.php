<?php

namespace App\Facade;

use Illuminate\Support\Facades\DB;
use App\Message;
use App\Employer\Post;
use Carbon\Carbon;

class OilMessages
{

    // Messages
    public function set($data, $fields = array())
    {
        if(isset($data['body']))
            $data['body'] = strip_tags($data['body']);

        if(Message::create($data))
        {
            if(isset($fields['table']))
            {
                switch ($fields['table'])
                {
                    case 'post':
                    case '1':
                    case 1:
                        Post::whereId($fields['id'])
                            ->update([$fields['update'][0] => $fields['update'][1]]);
                        break;

                    default:
                        break;
                }
            }

            return true;
        }
        else
            return false;
    }

    public function get($user_id = null, $custom_id = null, $readStatus = null, $type = null, $limit = 15, $typeOrder = 'desc')
    {
        $DataWhere      = array();
        $UserWhereIn    = array();

        if(isset($user_id) AND is_numeric($user_id))
            $UserWhereIn = [$user_id];
        elseif(isset($user_id) AND is_array($user_id))
            $UserWhereIn = $user_id;

        if(isset($custom_id) AND $custom_id)
            $DataWhere = array_merge($DataWhere, array('custom_id' => $custom_id));
        if(isset($readStatus) AND ($readStatus === 0 OR $readStatus === 1))
            $DataWhere = array_merge($DataWhere, array('is_read' => $readStatus));

        $DataWhere  = $this->OilSwitch($type, $DataWhere);
        $selectCase = $this->selectCase();

        if(count($UserWhereIn))
            return Message::select('id', 'user_id', 'admin_user_id', 'custom_id', 'body', 'type', 'is_read', DB::raw($selectCase), 'created_at', 'updated_at')
                                ->whereIn('user_id', $UserWhereIn)
                                ->where($DataWhere)
                                ->orderBy('created_at', $typeOrder)
                                ->paginate($limit);
        else
            return Message::select('id', 'user_id', 'admin_user_id', 'custom_id', 'body', 'type', 'is_read', DB::raw($selectCase), 'created_at', 'updated_at')
                                ->where($DataWhere)
                                ->orderBy('created_at', $typeOrder)
                                ->paginate($limit);
    }

    public function getTrashed($user_id = null, $custom_id = null, $readStatus = null, $type = null, $limit = 15, $typeOrder = 'desc')
    {
        $DataWhere = array();


        if(isset($user_id) AND $user_id)
            $DataWhere = array_merge($DataWhere, array('user_id' => $user_id));
        if(isset($custom_id) AND $custom_id)
            $DataWhere = array_merge($DataWhere, array('custom_id' => $custom_id));
        if(isset($readStatus) AND ($readStatus === 0 OR $readStatus === 1))
            $DataWhere = array_merge($DataWhere, array('is_read' => $readStatus));

        $DataWhere  = $this->OilSwitch($type, $DataWhere);
        $selectCase = $this->selectCase();

        return Message::select('id', 'user_id', 'admin_user_id', 'custom_id', 'body', 'type', 'is_read', DB::raw($selectCase), 'created_at', 'updated_at')
            ->where($DataWhere)
            ->orderBy('created_at', $typeOrder)
            ->onlyTrashed()
            ->paginate($limit);
    }

    public function getCount($user_id = null , $custom_id = null, $type = null , $readStatus = null )
    {
        $DataWhere = array();

        if(isset($user_id) AND $user_id)
            $DataWhere = array_merge($DataWhere, array('user_id' => $user_id));
        if(isset($custom_id) AND $custom_id)
            $DataWhere = array_merge($DataWhere, array('custom_id' => $custom_id));
        if($readStatus === 0 or $readStatus === 1)
            $DataWhere = array_merge($DataWhere, array('is_read' =>$readStatus));

        $DataWhere = $this->OilSwitch($type, $DataWhere);

        return Message::where($DataWhere)->count();
    }

    public function read($message_id)
    {
        $result = Message::whereId($message_id)->update(['is_read' => 1]);
        return $result ? true : false;
    }

    public function delete($message_id)
    {
        $result = Message::whereId($message_id)->delete();
        return $result ? true : false;
    }


    // Messages Default

    public function getDefault($where = [], $fields = '*', $limit = 15, $typeOrder = 'desc')
    {
        $table = DB::table('messages_default')->select($fields);

        if(count($where))
            $table->where($where);

        return $table->orderBy('created_at', $typeOrder)->paginate($limit);
    }

    public function getDefaultWithType($type = null)
    {
        $table = DB::table('messages_default');
        return $table->whereType($type)->first();
    }

    public function setMessagesDefault($body)
    {
        return DB::table('messages_default')->insert(['body' => trim($body), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
    }

    public function messagesDefaultDelete($message_default_id)
    {
        return DB::table('messages_default')->whereId($message_default_id)->delete();
    }

    public function getMessagesDefaultDelete($message_default_id)
    {
        return DB::table('messages_default')->whereId($message_default_id)->first();
    }

    public function getMessagesDefaultUpdate($message_default_id, $body)
    {
        return DB::table('messages_default')->whereId($message_default_id)->update(['body' => trim($body)]);
    }


    // Customise OilMessages

    private function selectCase()
    {
        return '(CASE
                            WHEN type = '.MESSAGE_TYPE_REJECT_POST.' THEN "'.MESSAGE_TYPE_REJECT_POST_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_APPROVE_POST.' THEN "'.MESSAGE_TYPE_APPROVE_POST_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_NOT_APPROVE_POST.' THEN "'.MESSAGE_TYPE_NOT_APPROVE_POST_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_DELETE_POST.' THEN "'.MESSAGE_TYPE_DELETE_POST_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_IMAGES_APPROVE.' THEN "'.MESSAGE_TYPE_IMAGES_APPROVE_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_IMAGES_DELETE.' THEN "'.MESSAGE_TYPE_IMAGES_DELETE_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_APPLIES_INVITED.' THEN "'.MESSAGE_TYPE_APPLIES_INVITED_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_APPLIES_REJECTED.' THEN "'.MESSAGE_TYPE_APPLIES_REJECTED_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_APPLIES_SEEN.' THEN "'.MESSAGE_TYPE_APPLIES_SEEN_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_CANDIDATE_APPLIES.' THEN "'.MESSAGE_TYPE_CANDIDATE_APPLIES_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_APPLIES_ASSESSMENT_IN_PROGRESS.' THEN "'.MESSAGE_TYPE_APPLIES_ASSESSMENT_IN_PROGRESS_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_COMPANY_APPROVED.' THEN "'.MESSAGE_TYPE_COMPANY_APPROVED_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_COMPANY_NOT_APPROVED.' THEN "'.MESSAGE_TYPE_COMPANY_NOT_APPROVED_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION.' THEN "'.MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION_ANSWER.' THEN "'.MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION_ANSWER_TEXT.'"
                            WHEN type = '.MESSAGE_TYPE_CANDIDATE_CONVERSATION_ANSWER.' THEN "'.MESSAGE_TYPE_CANDIDATE_CONVERSATION_ANSWER_TEXT.'"
                            ELSE NULL END) AS type_txt';
    }

    private function OilSwitch($type, $DataWhere)
    {
        switch ($type)
        {
            case 'post':
            case '1':
            case MESSAGE_TYPE_REJECT_POST:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_REJECT_POST));
                break;
            case 'images_approve':
            case '2':
            case MESSAGE_TYPE_IMAGES_APPROVE:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_IMAGES_APPROVE));
                break;
            case 'images_delete':
            case '3':
            case MESSAGE_TYPE_IMAGES_DELETE:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_IMAGES_DELETE));
                break;
            case 'approve_post':
            case '4':
            case MESSAGE_TYPE_APPROVE_POST:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_APPROVE_POST));
                break;
            case 'not_approve_post':
            case '5':
            case MESSAGE_TYPE_NOT_APPROVE_POST:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_NOT_APPROVE_POST));
                break;
            case 'deleted_post':
            case '6':
            case MESSAGE_TYPE_DELETE_POST:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_DELETE_POST));
                break;
            case 'applies_invited':
            case '7':
            case MESSAGE_TYPE_APPLIES_INVITED:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_APPLIES_INVITED));
                break;
            case 'applies_rejected':
            case '8':
            case MESSAGE_TYPE_APPLIES_REJECTED:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_APPLIES_REJECTED));
                break;
            case 'applies_seen':
            case '9':
            case MESSAGE_TYPE_APPLIES_SEEN:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_APPLIES_SEEN));
                break;
            case '10':
            case MESSAGE_TYPE_CANDIDATE_APPLIES:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_CANDIDATE_APPLIES));
                break;
            case '11':
            case MESSAGE_TYPE_APPLIES_ASSESSMENT_IN_PROGRESS:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_APPLIES_ASSESSMENT_IN_PROGRESS));
                break;
            case '12':
            case MESSAGE_TYPE_COMPANY_APPROVED:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_COMPANY_APPROVED));
                break;
            case '13':
            case MESSAGE_TYPE_COMPANY_NOT_APPROVED:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_COMPANY_NOT_APPROVED));
                break;
            case '14':
            case MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION));
                break;
            case '15':
            case MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION_ANSWER:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_COMPANY_CREATE_CONVERSATION_ANSWER));
                break;
            case '16':
            case MESSAGE_TYPE_CANDIDATE_CONVERSATION_ANSWER:
                $DataWhere = array_merge($DataWhere, array('type' => MESSAGE_TYPE_CANDIDATE_CONVERSATION_ANSWER));
                break;

            default:
                break;
        }

        return $DataWhere;
    }

}