<template>
<div class="comments-app container">
    <h1>This blog post</h1>
   <h3>Comments</h3>
   <!-- From -->
   <div class="comment-form row">
       <div class="col-md-12">
       <form name="form">
           
           <div>
               <textarea class="form-control" placeholder="Add comment..." required v-model="message"></textarea>
               <span class="text-danger" v-if="errorComment">{{errorComment}}</span>
           </div>
           <div>
               <input class="form-control" placeholder="Enter Name" type="text" v-model="name">
               <span class="text-danger" v-if="errorName">{{errorName}}</span>
           </div>
           <div>
               <input type="button" class="btn btn-outline-dark float-right" @click="saveComment" value="Add Comment">
           </div>
       </form>
       </div>
   </div>
   
   <!-- Comments List -->
   
   <div class="comments row" style="margin-bottom:10px;" v-bind:key="comment" v-for="(comment,index) in commentsData">
       <!-- Comment -->
       <div class="comment col-md-12">
           <!-- Comment Box -->
           <div class="comment-box bg-info" style="padding:10px;">
               <div class="comment-text">{{comment.comment}}</div>
               <div class="comment-footer">
                   <div class="comment-info">
                       <span class="comment-author">
                               <em>Commented By: {{ comment.name}}</em>
                           </span>
                       <span class="comment-date">{{ comment.date}}</span>
                   </div>
                   <div class="comment-actions">
                        <a class="btn btn-primary" v-on:click="openComment(comment.commentid)">Reply</a>
                           
                   </div>
               </div>
           </div>
           <!-- From -->
           <div class="comment-form comment-v" v-if="commentBoxs[comment.commentid]">
               <form name="form">
                   <div>
                       <textarea class="form-control" placeholder="Add comment..." required v-model="message"></textarea>
                       <span class="text-danger" v-if="errorReply" style="color:red">{{errorReply}}</span>
                   </div>
                   <div>
                       <input class="form-control" placeholder="Name" type="text" required v-model="name">
                       <span class="text-danger" v-if="errorReply">{{errorReplyName}}</span>
                   </div>
                   <div>
                       <input type="button" class="btn btn-success" v-on:click="replyComment(comment.commentid,index)" value="Add Comment">
                   </div>
               </form>
           </div>
           <!-- Comment - Reply -->
           <div v-if="comment.replies">
               
                <div class="comments" v-bind:key="replies" v-for="(replies) in comment.replies">
                    <div class="comment reply">
                        <!-- Comment Box -->
                        <div class="comment-box bg-light" style="padding-left:20px;">
                            <div class="comment-text text-primary">{{replies.comment}} test</div>
                            <div class="comment-footer">
                                <div class="comment-info">
                                    <span class="comment-author">
                                            Commented By: {{replies.name}}
                                        </span>
                                    <span class="comment-date">{{replies.date}}</span>
                                </div>
                                <div class="comment-actions">
                                        <a class="btn btn-danger" v-on:click="replyCommentBox(replies.commentid)">Reply</a>
                                </div>
                            </div>
                        </div>
                        <!-- From -->
                        <div class="comment-form reply" v-if="replyCommentBoxs[replies.commentid]">
                            <form name="form">
                                <div>
                                    <textarea class="form-control" placeholder="Add comment..." required v-model="message"></textarea>
                                    <span class="text-danger" v-if="errorReply">{{errorReply}}</span>
                                </div>
                                <div>
                                    <input class="form-control" placeholder="Name" type="text" v-model="name">
                                    <span class="text-danger" v-if="errorReply">{{errorReplyName}}</span>
                                </div>
                                <div>
                                    <input type="button" class="btn btn-success" v-on:click="replyCommentFR(replies.commentid,index)" value="Add Comment">
                                </div>
                            </form>
                        </div>
                    </div>

                        <!-- Comment - Reply FR-->
                    <div v-if="replies.repliesFR" style="margin-bottom:20px;">
                        <div class="comments" v-bind:key="repliesFR" v-for="(repliesFR) in replies.repliesFR">
                            <div class="comment reply">
                                <!-- Comment Box -->
                                <div class="comment-box" style="padding-left:30px;">
                                    <div class="comment-text" style="color: red">{{repliesFR.comment}}</div>
                                    <div class="comment-footer">
                                        <div class="comment-info">
                                            <span class="comment-author">
                                                   Commented By:  {{repliesFR.name}}
                                                </span>
                                            <span class="comment-date">{{repliesFR.date}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
           </div>
           
       </div>
   </div>
</div>
</template>
<script>

var _ = require('lodash');
export default {
   props: ['commentUrl'],
   data() {
       return {
           comments: [],
           commentreplies: [],
           comments: 0,
           commentBoxs: [],
           name: null,
           message: null,
           replyCommentBoxs: [],
           commentsData: [],
           errorComment: null,
           errorName: null,
           errorReply: null,
           errorReplyName: null,
           token: $('meta[name="csrf-token"]').attr('content')
       }
   },
   methods: {
       fetchComments() {
           this.$http.get('comments/blog').then(res => {
               console.log(res.data)
               this.commentData = res.data;
               this.commentsData = _.orderBy(res.data, ['date'], ['desc']);
               this.comments = 1;
           });
           
       },
       openComment(index) {
            console.log(index)
            if (this.commentBoxs[index]) {
                Vue.set(this.commentBoxs, index, 0);
            } else {
                Vue.set(this.commentBoxs, index, 1);
            }
       },
       replyCommentBox(index) {
           
            if (this.replyCommentBoxs[index]) {
                Vue.set(this.replyCommentBoxs, index, 0);
            } else {
                Vue.set(this.replyCommentBoxs, index, 1);
            }
       },
       saveComment() {
           if (this.message != null && this.message != ' ' && this.name != null && this.name != ' ') {
               this.errorComment = null;
               this.errorName = null;
               this.$http.post('comments', {
                   comment: this.message,
                   name: this.name,
                   _token: this.token
               },).then(res => {
                   console.log(res.data.status)
                   if (res.data.status) {
                       console.log(this.commentsData.length)
                       if(this.commentsData.length > 0){
                        this.commentsData.push({ "commentid": res.data.commentId, "name": this.name, "comment": this.message, "date": res.data.date, "reply": 0, "replies": [] });
                       }else{
                           this.commentsData.push({ "commentid": res.data.commentId, "name": this.name, "comment": this.message, "date": res.data.date, "reply": 0, "replies": [] });
                       }
                       this.commentsData = _.orderBy(this.commentsData, ['date'], ['desc']);
                       this.message = null;
                       this.name = null;
                       
                   }
               });
           } else {
               this.errorComment = "Please enter a comment to save";
               this.errorName = "Please enter a name to save";
           }
       },
       replyComment(commentId, index) {
           console.log(commentId)
           if (this.message != null && this.message != ' ' && this.name != null && this.name != ' ') {
               this.errorReply = null;
               this.errorReplyName = null;
               this.$http.post('comments', {
                   comment: this.message,
                   name: this.name,
                   reply_id: commentId,
                   _token: this.token
               }).then(res => {
                   if (res.data.status) {
                       if (!this.commentsData[index].reply) {
                           this.commentsData[index].replies.push({ "commentid": res.data.commentId, "name": this.name, "comment": this.message, "date": res.data.date, "repliesFR": [] });
                           this.commentsData[index].reply = 1;
                           Vue.set(this.replyCommentBoxs, commentId, 0);
                           Vue.set(this.commentBoxs, commentId, 0);
                       } else {
                           this.commentsData[index].replies.push({ "commentid": res.data.commentId, "name": this.name, "comment": this.message, "date": res.data.date, "repliesFR": [] });
                           Vue.set(this.replyCommentBoxs, commentId, 0);
                           Vue.set(this.commentBoxs, commentId, 0);
                       }
                       this.commentsData[index].replies = _.orderBy(this.commentsData[index].replies, ['date'], ['desc']);
                       this.message = null;
                       this.name = null
                   }
               });
           } else {
               this.errorReply = "Please enter a comment to save";
               this.errorReplyName = "Please enter a name to save";
           }
       },
       replyCommentFR(commentId, index) {
           console.log(commentId)
           if (this.message != null && this.message != ' ' && this.name != null && this.name != ' ') {
               this.errorReply = null;
               this.errorReplyName = null;
               this.$http.post('comments', {
                   comment: this.message,
                   name: this.name,
                   reply_id: commentId,
                   _token: this.token
               }).then(res => {
                   if (res.data.status) {
                       if (!this.commentsData[index].reply) {
                           this.commentsData[index].replies[index].repliesFR.push({ "commentid": res.data.commentId, "name": this.name, "comment": this.message, "date": res.data.date });
                           this.commentsData[index].reply = 1;
                           Vue.set(this.replyCommentBoxs, commentId, 0);
                           Vue.set(this.commentBoxs, commentId, 0);
                       } else {
                           this.commentsData[index].replies[index].repliesFR.push({ "commentid": res.data.commentId, "name": this.name, "comment": this.message, "date": res.data.date });
                           Vue.set(this.replyCommentBoxs, commentId, 0);
                           Vue.set(this.commentBoxs, commentId, 0);
                       }
                       this.commentsData[index].replies[index].repliesFR = _.orderBy(this.commentsData[index].replies[index].repliesFR, ['date'], ['desc']);
                       this.message = null;
                       this.name = null
                   }
               });
           } else {
               this.errorReply = "Please enter a comment to save";
               this.errorReplyName = "Please enter a name to save";
           }
       },
   },
   mounted() {
      console.log("mounted");
      this.fetchComments();
   }
}
</script>