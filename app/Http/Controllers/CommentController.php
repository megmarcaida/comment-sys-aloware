<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    #get all comments
    public function index()
    {
        $comments = Comment::orderBy('created_at', 'DESC')->get();
        $commentsData = [];
        $nested3 = false;
        foreach ($comments as $key) {
            $name = $key->name;
            $replies = $this->replies($key->id);
            $reply = 0;
           
            if(isset($replies)){
                if(sizeof($replies) > 0){
                    $reply = 1;
                }
                
            }
            if($key->reply_id != 0){
                continue;
            }
            
            array_push($commentsData,[
                "name" => $name,
                "commentid" => $key->id,
                "comment" => $key->comment,
                "reply" => $reply,
                "replies" => $replies,
                "date" => $key->created_at->toDateTimeString(),
                "reply_id" => $key->reply_id
            ]);
                   
        }
        $collection = collect($commentsData);
        return $collection->sortBy('commentid');
    }

    #replies
    protected function replies($commentId)
    {
        $comments = Comment::where('reply_id',$commentId)->get();
        $replies = [];
        $repliesFR = [];
        $replyFR = [];
        foreach ($comments as $key) {
            $name = $key->name;
            $repliesFR = $this->repliesFR($key->id);

            if(isset($repliesFR)){
                if(sizeof($repliesFR) > 0){
                    $replyFR = 1;
                }
            }

            array_push($replies,[
                "name" => $name,
                "commentid" => $key->id,
                "comment" => $key->comment,
                "replyFR" => $replyFR,
                "repliesFR" => $repliesFR,
                "date" => $key->created_at->toDateTimeString(),
                "reply_id" => $key->reply_id
            ]);
    
        
        }
        $collection = collect($replies);
        return $collection->sortBy('commentid');
    }

    #replies from replies
    protected function repliesFR($commentId)
    {
        $comments = Comment::where('reply_id',$commentId)->get();
        $replies = [];
        foreach ($comments as $key) {
            $name = $key->name;
               
            array_push($replies,[
                "name" => $name,
                "commentid" => $key->id,
                "comment" => $key->comment,
                "date" => $key->created_at->toDateTimeString()
            ]);
    
        
        }
        $collection = collect($replies);
        return $collection->sortBy('commentid');
    }

    #store comment
    public function store(Request $request)
    {
        $this->validate($request, [
        'name' => 'required',
        'comment' => 'required',
        'reply_id' => 'filled',
        ]);
        $comment = Comment::create($request->all());
        if($comment)
            return [ "status" => "true","commentId" => $comment->id,"date"=>$comment->created_at->toDateTimeString() ];
    }

}
