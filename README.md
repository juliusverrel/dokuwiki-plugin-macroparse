dokuwiki-plugin-macroparse
==========================

Replaces macro expressions before dokuwiki rendering, allowing to use macros in other plugin commands. 

Examples
--------

**Example 1:** Using the [include plugin](https://www.dokuwiki.org/plugin:include)

    <macroparse>
    {{page>user:@USER@:about_me}}
    {{page>events:@TODAY@}}
    </macroparse>
    
**Example 2:** Using the [struct plugin](https://www.dokuwiki.org/plugin:struct)

    <macroparse>
    ---- struct table ----
    schema: project
    cols: %pageid%, members, info 
    filter: members = @NAME@
    ----
    </macroparse>

**Example 2:** Using the [change plugin](https://www.dokuwiki.org/plugin:changes)
     
     <macroparse>
     {{changes> user = @USER@}}
     </macroparse>
     
     
Available placeholders
----------------------

Page-specific

* ``@ID@``: Full page id (e.g., `user:peter:cv`)
* ``@NS@``: Full namespace (e.g., `user:peter`)
* ``@LASTNS@``: "Last" namespace (e.g., `peter`)
* ``@PAGE@``: Page name (e.g., `cv`)

Date-specific

* ``@TODAY@``: Full date (Y-m-d)
* ``@YEAR@``
* ``@MONTH@``
* ``@WEEK@``
* ``@DAY@``
                    
User-specific

* ``@FULLNAME@``: User's full name (e.g. Peter Pan)
* ``@CLEANNAME@``: as above, after applying ``cleanID``
* ``@USER@``: User id (login) (e.g., ppan)
* ``@EMAIL@``: Email address (e.g., peter.pan@story.com)
* ``@EMAILNAME@``: part before @ (e.g., peter.pan)
* ``@EMAILSHORT@``: part before @ (e.g., peter.pan), + "student" if email address is @student...
* ``@CLEANEMAIL@``: as above, after applying ``cleanID``
* ``@CLEANEMAILNAME@``: as above, after applying ``cleanID``
* ``@CLEANEMAILSHORT@``: as above, after applying ``cleanID``

     
