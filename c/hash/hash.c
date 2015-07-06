#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <assert.h>
#define TEST(tcase) printf(">>> [START CASE] " tcase "<<<\n")
#define PASS(tcase) printf(">>> [PASSED] " tcase " <<<\n")

#define HASH_TABLE_INIT_SIZE 6
int hash_str(char *key)
{
    int hash = 0;

    char *cur = key;

    while(*cur != '\0')
    {
        hash += *cur;
        ++cur;
    }

    return hash;
}
#define HASH_INDEX(ht, key) (hash_str((key)) % (ht)->size)

#if defined(DEBUG)
#  define LOG_MSG printf
#else
#  define LOG_MSG(...)
#endif

#define SUCCESS 0
#define FAILED -1

typedef struct _Bucket
{
    char *key;
    void *value;
    struct _Bucket *next;
} Bucket;

typedef struct _HashTable
{
    int size;       // 哈希表的大小
    int elem_num;   // 已经保存元素的个数
    Bucket **buckets;
} HashTable;

int hash_init(HashTable *ht)
{
    ht->size        = HASH_TABLE_INIT_SIZE;
    ht->elem_num    = 0;
    ht->buckets     = (Bucket **)calloc(ht->size, sizeof(Bucket *));

    if(ht->buckets == NULL) return FAILED;

    LOG_MSG("[init]\tsize: %i\n", ht->size);

    return SUCCESS;
}

int hash_insert(HashTable *ht, char *key, void *value)
{
    // check if we need to resize the hashtable
    resize_hash_table_if_needed(ht);

    int index = HASH_INDEX(ht, key);

    Bucket *org_bucket = ht->buckets[index];
    Bucket *tmp_bucket = org_bucket;

    // check if the key exits already
    while(tmp_bucket)
    {
        if(strcmp(key, tmp_bucket->key) == 0)
        {
            LOG_MSG("[update]\tkey: %s\n", key);
            tmp_bucket->value = value;

            return SUCCESS;
        }

        tmp_bucket = tmp_bucket->next;
    }

    Bucket *bucket = (Bucket *)malloc(sizeof(Bucket));

    bucket->key   = key;
    bucket->value = value;
    bucket->next  = NULL;

    ht->elem_num += 1;

    if(org_bucket != NULL)
    {
        LOG_MSG("[collision]\tindex:%d key:%s\n", index, key);
        bucket->next = org_bucket;
    }

    ht->buckets[index]= bucket;

    LOG_MSG("[insert]\tindex:%d key:%s\tht(num:%d)\n",
        index, key, ht->elem_num);

    return SUCCESS;
}

int hash_resize(HashTable *ht)
{
    // double the size
    int org_size = ht->size;
    ht->size = ht->size * 2;
    ht->elem_num = 0;

    LOG_MSG("[resize]\torg size: %i\tnew size: %i\n", org_size, ht->size);

    Bucket **buckets = (Bucket **)calloc(ht->size, sizeof(Bucket **));

    Bucket **org_buckets = ht->buckets;
    ht->buckets = buckets;

    int i = 0;
    for(i=0; i < org_size; ++i)
    {
        Bucket *cur = org_buckets[i];
        Bucket *tmp;
        while(cur) 
        {
            // rehash: insert again
            hash_insert(ht, cur->key, cur->value);

            // free the org bucket, but not the element
            tmp = cur;
            cur = cur->next;
            free(tmp);
        }
    }
    free(org_buckets);

    LOG_MSG("[resize] done\n");

    return SUCCESS;
}

// if the elem_num is almost as large as the capacity of the hashtable
// we need to resize the hashtable to contain enough elements
void resize_hash_table_if_needed(HashTable *ht)
{
    if(ht->size - ht->elem_num < 1)
    {
        hash_resize(ht);    
    }
}

int hash_lookup(HashTable *ht, char *key, void **result)
{
    int index = HASH_INDEX(ht, key);
    Bucket *bucket = ht->buckets[index];

    if(bucket == NULL) goto failed;

    while(bucket)
    {
        if(strcmp(bucket->key, key) == 0)
        {
            LOG_MSG("[lookup]\t found %s\tindex:%i value: %p\n",
                key, index, bucket->value);
            *result = bucket->value;    

            return SUCCESS;
        }

        bucket = bucket->next;
    }

failed:
    LOG_MSG("[lookup]\t key:%s\tfailed\t\n", key);
    return FAILED;
}



int hash_remove(HashTable *ht, char *key)
{
    int index = HASH_INDEX(ht, key);
    Bucket *bucket  = ht->buckets[index];
    Bucket *prev    = NULL;

    if(bucket == NULL) return FAILED;

    // find the right bucket from the link list 
    while(bucket)
    {
        if(strcmp(bucket->key, key) == 0)
        {
            LOG_MSG("[remove]\tkey:(%s) index: %d\n", key, index);

            if(prev == NULL)
            {
                ht->buckets[index] = bucket->next;
            }
            else
            {
                prev->next = bucket->next;
            }
            free(bucket);

            return SUCCESS;
        }

        prev   = bucket;
        bucket = bucket->next;
    }

    LOG_MSG("[remove]\t key:%s not found remove \tfailed\t\n", key);
    return FAILED;
}

int hash_destroy(HashTable *ht)
{
    int i;
    Bucket *cur = NULL;
    Bucket *tmp = NULL;

    for(i=0; i < ht->size; ++i)
    {
        cur = ht->buckets[i];
        while(cur)
        {
            tmp = cur;
            cur = cur->next;
            free(tmp);
        }
    }
    free(ht->buckets);

    return SUCCESS;
}







int main(int argc, char **argv)
{
    HashTable *ht = (HashTable *)malloc(sizeof(HashTable));
    int result = hash_init(ht);

    assert(result == SUCCESS);

    /* Data */
    int  int1 = 10;
    int  int2 = 20;
    char str1[] = "Hello TIPI";
    char str2[] = "Value";
    /* to find data container */
    int *j = NULL;
    char *find_str = NULL;

    /* Test Key insert */
    TEST("Key insert");
    hash_insert(ht, "KeyInt", &int1);
    hash_insert(ht, "asdfKeyStrass", str1);
    hash_insert(ht, "K13eyStras", str1);
    hash_insert(ht, "KeyStr5", str1);
    hash_insert(ht, "KeyStr", str1);
    PASS("Key insert");

    /* Test key lookup */
    TEST("Key lookup");
    hash_lookup(ht, "KeyInt", (void **)&j);
    hash_lookup(ht, "KeyStr", (void **)&find_str);

    assert(strcmp(find_str, str1) == 0);
    assert(*j = int1);
    PASS("Key lookup");

    /* Test Key update */
    TEST("Test key update");
    hash_insert(ht, "KeyInt", &int2);
    hash_lookup(ht, "KeyInt", (void **)&j);
    assert(*j = int2);
    PASS("Test key update");

    TEST(">>>    Test key not found     <<< ");
    result = hash_lookup(ht, "non-exits-key", (void **)&j);
    assert(result == FAILED);
    PASS("non-exist-key lookup");

    TEST("Test key not found after remove");
    char strMyKey[] = "My-Key-Value";
    find_str = NULL;
    hash_insert(ht, "My-Key", &strMyKey);
    result = hash_remove(ht, "My-Key");
    assert(result == SUCCESS);

    result = hash_lookup(ht, "My-Key", (void **)&find_str);
    assert(find_str == NULL);
    assert(result == FAILED);
    PASS("Test key not found after remove");

    PASS(">>>    Test key not found     <<< ");

    TEST("Add many elements and make hashtable rehash");
    hash_insert(ht, "a1", &int2);
    hash_insert(ht, "a2", &int1);
    hash_insert(ht, "a3", &int1);
    hash_insert(ht, "a4", &int1);
    hash_insert(ht, "a5", &int1);
    hash_insert(ht, "a6", &int1);
    hash_insert(ht, "a7", &int1);
    hash_insert(ht, "a8", str2);
    hash_insert(ht, "a9", &int1);
    hash_insert(ht, "a10", &int1);
    hash_insert(ht, "a11", &int1);
    hash_insert(ht, "a12", &int1);
    hash_insert(ht, "a13", &int1);
    hash_insert(ht, "a14", &int1);
    hash_insert(ht, "a15", &int1);
    hash_insert(ht, "a16", &int1);
    hash_insert(ht, "a17", &int1);
    hash_insert(ht, "a18", &int1);
    hash_insert(ht, "a19", &int1);
    hash_insert(ht, "a20", &int1);
    hash_insert(ht, "a21", &int1);
    hash_insert(ht, "a22", &int1);
    hash_insert(ht, "a23", &int1);
    hash_insert(ht, "a24", &int1);
    hash_insert(ht, "a24", &int1);
    hash_insert(ht, "a24", &int1);
    hash_insert(ht, "a25", &int1);
    hash_insert(ht, "a26", &int1);
    hash_insert(ht, "a27", &int1);
    hash_insert(ht, "a28", &int1);
    hash_insert(ht, "a29", &int1);
    hash_insert(ht, "a30", &int1);
    hash_insert(ht, "a31", &int1);
    hash_insert(ht, "a32", &int1);
    hash_insert(ht, "a33", &int1);

    hash_lookup(ht, "a23", (void **)&j);
    assert(*j = int1);
    hash_lookup(ht, "a30", (void **)&j);
    assert(*j = int1);
    PASS("Add many elements and make hashtable rehash");

    hash_destroy(ht);
    free(ht);

    printf("Woohoo, It looks like HashTable works properly\n");

    return 0;
}
