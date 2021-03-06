<?php

namespace App\Models;


class Articles extends BaseModels
{
    /**
     * @var string
     */
    protected $table = 'article';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $hidden = ['click_num'];


    /**
     *  $guarded $fillable 属性只是在 Eloquent 的 create 方法时才有用，save 的时候是没用的
     *
     *  批量赋值的英文名称是Mass Assignment，所谓的批量赋值是指当我们将一个数组发送到模型类用于创建新的模型实例的时候（通常是表单请求数据）
     *
     * 那么如果我们确实想要修改定义在$guarded中的属性怎么办？答案是使用save方法。
     *
     */


    /**
     *
     * 这两个字段的值，不能被批量修改
     * save(),update(),都不行
     * @var array
     */
    protected $guarded  = ['user_id','id'];


    /**
     * 这里面的字段 是 能够被批量修改的,其他的就是不能被修改的
     * @var array
     */
    //protected $fillable = ['title'];

    //protected $with = ['User'];

    //public $timestamps = false;


    /**
     * The attributes that should be mutated to dates.
     * 在 $dates 这个数组里面的所有的字段，在外显示的时候，都会以Carbon 的格式显示
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * 将从数据库 取出的数据的某些个字段，转换成你希望显示的数据类型，不管它们在数据库里面的字段类型
     *
     * @var array
     *
     *  类型有：  integer, real, float, double, string, boolean, object, array, collection, date and datetime.
     */
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Eloquent也支持模型事件——当模型被创建、更新或删除的时候触发相应事件，
     * Eloquent目前支持八种事件类型：creating、created、updating、updated、saving、saved、deleting、deleted。
     * deleting和deleted很好理解，在删除模型时触发，deleting在删除操作前执行，deleted在删除完成后执行。
     * 当创建模型时，依次执行saving、creating、created和saved，
     * 同理在更新模型时依次执行saving、updating、updated和saved。
     * 无论是使用批量赋值（create/update）还是直接调用save方法，都会触发对应事件（前提是注册了相应的模型事件）。
     */
    /**
     * 模型事件
     * 写在模型的静态方法boot()里
     */
    public static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        /**
         *  在article 这个模型  create 方法的时候会被执行
         *
         *  在加入一条数据到数据库时，先执行的是 creating 方法，再执行 created
         */

        static::creating(function($article){

            if(!empty($article->id)){

                debug('article creating is false');
                return false;
            }
            debug('article creating');
        });

        static::created(function($article){

            debug('article created');
        });


        /**
         *  在 article 这个模型的 update 方法 被调用的时候会被执行
         */
        static::updating(function($article){
            debug($article);
            debug('article updating');
        });

        /**
         *  在 article 这个模型的 delete 方法 被调用的时候会被执行
         */
        static::deleting(function($article){
            debug($article);
            debug('article deleting');
        });
    }


    /**
     * 第一个参数：B
     * 第二个参数：A 与 B外键 ， 如果不写，那么默认为 第四个参数名_id （user_id）
     * 第三个参数：B 与 A 关联的外键的 键,如果不写，默认就是 B 模型的 主键
     * 第四个参数：如果不写，默认就是函数名
     *
     * 查看表 下面两个语句 是等价 的
     */
    public function user(){
        return $this->belongsTo('App\Models\User','user_id','id','user');

        //return $this->belongsTo('App\Models\User');
    }

    /**
     * 修改器
     *  下面的两个 getNameAttribute,setNameAttribute 方法就是修改器
     *  它们只是对从数据库取出数据后的某个字段的  get\set 操作，并不会影响数据库中，该字段的本身的值
     *
     */
    /**
     * @param $value
     * @return string
     *
     * 取得 title 字段的时候，对它的操作
     */
    public function getTitleAttribute($value){

        return ucfirst($value);
    }


    /**
     * @param $value
     * @return mixed
     *
     * 重新设置 title 字段值的时候，对它的操作
     */
    public function setTitleAttribute($value){

        return $this->attributes['title'] = strtolower($value);

    }


    /**
     * 查询作用域 scope
     *
     * 实际就是对一些查询条件的封装,定义成一个个方法，这样就不必每次都写那些查询条件了，只需要对这个method 链接就 可以了
     *
     * demo: 这里只是把 where('click_num','>',15) 这个条件，封装成了一个方法
     *
     * 在Controller 里面 调用,把方法前面的  scope去掉 ，就是调用的方法名
     *
     * Article::popular()->orderBy('id')->get();
     */
    public function scopePopular($query){
        return $query->where('click_num','>=',10);
    }

}
