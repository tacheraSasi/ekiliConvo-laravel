
### Laravel Eloquent Cheat Sheet

#### Basic Eloquent Methods

1. **Creating Records**

   - **`create(array $attributes)`**
     - **Usage:** `Model::create(['name' => 'John Doe']);`
     - **Why Use:** Inserts a new record and returns the instance of the model.

   - **`insert(array $values)`**
     - **Usage:** `Model::insert(['name' => 'Jane Doe']);`
     - **Why Use:** Inserts multiple records at once. Returns `true` on success but does not return the model instances.

   - **`make(array $attributes)`**
     - **Usage:** `$model = Model::make(['name' => 'John Doe']);`
     - **Why Use:** Creates an instance of the model without saving it to the database.

   - **`firstOrNew(array $attributes, array $values = [])`**
     - **Usage:** 
       ```php
       $model = Model::firstOrNew(['email' => 'johndoe@example.com'], ['name' => 'John Doe']);
       ```
     - **Why Use:** Retrieves the first record that matches the attributes or creates a new instance if none exists.

   - **`firstOrCreate(array $attributes, array $values = [])`**
     - **Usage:** 
       ```php
       $model = Model::firstOrCreate(['email' => 'johndoe@example.com'], ['name' => 'John Doe']);
       ```
     - **Why Use:** Retrieves the first record that matches the attributes or creates it if none exists.

   - **`updateOrCreate(array $attributes, array $values = [])`**
     - **Usage:** 
       ```php
       $model = Model::updateOrCreate(['email' => 'johndoe@example.com'], ['name' => 'John Doe']);
       ```
     - **Why Use:** Updates the record if it exists or creates it if it does not.

2. **Reading Records**

   - **`all()`**
     - **Usage:** `Model::all();`
     - **Why Use:** Retrieves all records from the database as a collection.

   - **`find($id)`**
     - **Usage:** `Model::find(1);`
     - **Why Use:** Retrieves a single record by its primary key.

   - **`findOrFail($id)`**
     - **Usage:** `Model::findOrFail(1);`
     - **Why Use:** Retrieves a single record by its primary key or throws a `ModelNotFoundException`.

   - **`where(string $column, mixed $value)`**
     - **Usage:** `Model::where('status', 'active')->get();`
     - **Why Use:** Filters records based on specified conditions.

   - **`whereIn(string $column, array $values)`**
     - **Usage:** `Model::whereIn('status', ['active', 'pending'])->get();`
     - **Why Use:** Filters records where the column's value is within a specified array.

   - **`whereNotIn(string $column, array $values)`**
     - **Usage:** `Model::whereNotIn('status', ['inactive'])->get();`
     - **Why Use:** Filters records where the column's value is not in a specified array.

   - **`orWhere(string $column, mixed $value)`**
     - **Usage:** `Model::where('status', 'active')->orWhere('status', 'pending')->get();`
     - **Why Use:** Combines multiple `where` conditions with an OR clause.

   - **`first()`**
     - **Usage:** `Model::where('status', 'active')->first();`
     - **Why Use:** Retrieves the first record that matches the query.

   - **`firstOrFail()`**
     - **Usage:** `Model::where('status', 'active')->firstOrFail();`
     - **Why Use:** Retrieves the first record that matches the query or throws an exception.

   - **`pluck(string $column)`**
     - **Usage:** `Model::pluck('name');`
     - **Why Use:** Retrieves a list of values for a given column.

   - **`count()`**
     - **Usage:** `Model::where('status', 'active')->count();`
     - **Why Use:** Returns the number of records that match the query.

   - **`exists()`**
     - **Usage:** `Model::where('email', 'johndoe@example.com')->exists();`
     - **Why Use:** Checks if any records exist that match the query.

   - **`doesntExist()`**
     - **Usage:** `Model::where('email', 'johndoe@example.com')->doesntExist();`
     - **Why Use:** Checks if no records exist that match the query.

   - **`get()`**
     - **Usage:** `Model::where('status', 'active')->get();`
     - **Why Use:** Executes the query and returns the results as a collection.

   - **`getModel()`**
     - **Usage:** `$query->getModel();`
     - **Why Use:** Returns the model instance for the query builder.

   - **`toSql()`**
     - **Usage:** `Model::where('status', 'active')->toSql();`
     - **Why Use:** Returns the SQL query as a string without executing it.

3. **Updating Records**

   - **`update(array $attributes)`**
     - **Usage:** `Model::where('id', 1)->update(['name' => 'John Doe']);`
     - **Why Use:** Updates existing records based on the conditions.

   - **`save()`**
     - **Usage:** 
       ```php
       $model = Model::find(1);
       $model->name = 'Updated Name';
       $model->save();
       ```
     - **Why Use:** Saves the model instance to the database. Can be used for both creating and updating.

   - **`increment(string $column, int $amount = 1, array $extra = [])`**
     - **Usage:** `Model::where('id', 1)->increment('views');`
     - **Why Use:** Increments a column's value by a specified amount.

   - **`decrement(string $column, int $amount = 1, array $extra = [])`**
     - **Usage:** `Model::where('id', 1)->decrement('views');`
     - **Why Use:** Decrements a column's value by a specified amount.

   - **`touch()`**
     - **Usage:** 
       ```php
       $model = Model::find(1);
       $model->touch();
       ```
     - **Why Use:** Updates the `updated_at` timestamp of the model.

4. **Deleting Records**

   - **`delete()`**
     - **Usage:** `Model::destroy(1);`
     - **Why Use:** Deletes a record by its primary key.

   - **`destroy(array|int $ids)`**
     - **Usage:** `Model::destroy([1, 2, 3]);`
     - **Why Use:** Deletes multiple records by their IDs.

   - **`forceDelete()`**
     - **Usage:** 
       ```php
       $model = Model::withTrashed()->find(1);
       $model->forceDelete();
       ```
     - **Why Use:** Permanently deletes a soft-deleted record.

   - **`softDelete()`**
     - **Usage:** 
       ```php
       $model = Model::find(1);
       $model->delete();
       ```
     - **Why Use:** Soft deletes the model, marking it as deleted without removing it from the database.

#### Relationships

1. **Defining Relationships**
   - **`hasMany()`**
     - **Usage:** 
       ```php
       public function comments() {
           return $this->hasMany(Comment::class);
       }
       ```
     - **Why Use:** Defines a one-to-many relationship.

   - **`belongsTo()`**
     - **Usage:** 
       ```php
       public function user() {
           return $this->belongsTo(User::class);
       }
       ```
     - **Why Use:** Defines an inverse one-to-many relationship.

   - **`belongsToMany()`**
     - **Usage:** 
       ```php
       public function roles() {
           return $this->belongsToMany(Role::class);
       }
       ```
     - **Why Use:** Defines a many-to-many relationship.

   - **`hasOne()`**
     - **Usage:** 
       ```php
       public function profile() {
           return $this->hasOne(Profile::class);
       }
       ```
     - **Why Use:** Defines a one-to-one relationship.

   - **`morphMany()`**
     - **Usage:** 
       ```php
       public function comments() {
           return $this->morphMany(Comment::class, 'commentable');
       }
       ```
     - **Why Use:** Defines a polymorphic one-to-many relationship.

   - **`morphTo()`**
     - **Usage:** 
       ```php
       public function commentable() {
           return $this->morphTo();
       }
       ```
     - **Why Use:** Defines a polymorphic inverse relationship.

   - **`hasManyThrough()`**
     - **Usage:** 
       ```php
       public function posts() {
           return $this->hasManyThrough(Post::class, Comment::class);
       }
       ```
     - **Why Use:** Defines a "has-many-through"

 relationship.

2. **Eager Loading Relationships**
   - **`with()`**
     - **Usage:** 
       ```php
       $users = User::with('posts')->get();
       ```
     - **Why Use:** Eager loads relationships to prevent N+1 query issues.

   - **`load()`**
     - **Usage:** 
       ```php
       $user = User::find(1);
       $user->load('posts');
       ```
     - **Why Use:** Loads relationships for an already retrieved model.

   - **`loadMissing()`**
     - **Usage:** 
       ```php
       $user->loadMissing('posts');
       ```
     - **Why Use:** Loads relationships if they haven’t been loaded yet.

#### Query Scopes

1. **Local Scopes**
   - **Defining a Local Scope**
     - **Usage:** 
       ```php
       public function scopeActive($query) {
           return $query->where('status', 1);
       }
       ```
     - **Why Use:** Allows you to define common query constraints.

   - **Using Local Scopes**
     - **Usage:** 
       ```php
       $activeUsers = User::active()->get();
       ```

2. **Global Scopes**
   - **Defining a Global Scope**
     - **Usage:** 
       ```php
       use Illuminate\Database\Eloquent\Builder;
       use Illuminate\Database\Eloquent\Model;
       use Illuminate\Database\Eloquent\Scope;

       class ActiveScope implements Scope {
           public function apply(Builder $builder, Model $model) {
               $builder->where('active', 1);
           }
       }
       ```
     - **Why Use:** Automatically applies certain constraints to all queries for a model.

   - **Using Global Scopes**
     - **Usage:** 
       ```php
       protected static function booted() {
           static::addGlobalScope(new ActiveScope);
       }
       ```

#### Other Useful Methods

1. **Transaction Handling**
   - **`DB::transaction()`**
     - **Usage:** 
       ```php
       DB::transaction(function () {
           // Perform multiple database operations
       });
       ```
     - **Why Use:** Groups multiple database operations into a single transaction.

2. **Timestamps**
   - **`timestamps`**
     - **Usage:** 
       ```php
       public $timestamps = true;
       ```
     - **Why Use:** Automatically manages `created_at` and `updated_at` columns.

3. **Soft Deletes**
   - **Enabling Soft Deletes**
     - **Usage:** 
       ```php
       use Illuminate\Database\Eloquent\SoftDeletes;

       class User extends Model {
           use SoftDeletes;
       }
       ```
     - **Why Use:** Allows soft deletion of records.

   - **Retrieving Soft Deleted Models**
     - **Usage:** 
       ```php
       $users = User::withTrashed()->get();
       ```
     - **Why Use:** Retrieves both soft deleted and active records.

   - **Restoring Soft Deleted Models**
     - **Usage:** 
       ```php
       $user = User::withTrashed()->find(1);
       $user->restore();
       ```

4. **JSON Columns**
   - **`json`**
     - **Usage:** 
       ```php
       $user = User::find(1);
       $preferences = $user->preferences; // Assuming preferences is a JSON column
       ```
     - **Why Use:** Retrieves JSON data stored in a database column.

5. **Aggregate Functions**
   - **`sum(string $column)`**
     - **Usage:** `Model::where('status', 'active')->sum('price');`
     - **Why Use:** Calculates the sum of a specified column.

   - **`avg(string $column)`**
     - **Usage:** `Model::where('status', 'active')->avg('price');`
     - **Why Use:** Calculates the average of a specified column.

   - **`max(string $column)`**
     - **Usage:** `Model::where('status', 'active')->max('price');`
     - **Why Use:** Retrieves the maximum value of a specified column.

   - **`min(string $column)`**
     - **Usage:** `Model::where('status', 'active')->min('price');`
     - **Why Use:** Retrieves the minimum value of a specified column.

---
