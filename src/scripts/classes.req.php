<?php
/* User($db, $id, $type)
		get_groups($db)
 * Torrents($db, $order_by, $order, $limit, $offset, $search)
		num_rows($db, $search)
 * Categories($db)
 * Languages($db, $order_by, $order)
 * Torrent($db, $id)
 * XDCC($db)
 * Groups($db)
 * Group($db, $id)
		get_members($db)
		get_likes($db)
		get_likes_user_id_ip_list($db)
		get_total_file_likes($db)
 * User_level($db, $id)
 * 
 *
 */

 class Batoto {
	protected $db = null;
	
	public function __construct($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT * FROM batoto_comics ORDER BY title ASC "); 
		
		foreach ($results as $i => $genre) {
			$this->{$i} = new \stdClass();
			foreach ($genre as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}	
}

class Genres {
	protected $db = null;
	
	public function __construct($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT * FROM mangadex_genres "); 
		
		foreach ($results as $i => $genre) {
			$this->{$i} = new \stdClass();
			foreach ($genre as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}	
}

class Mangas {
	protected $db = null;
	protected $limit = 50;
	protected $search = array();
	protected $order_by = "manga_name";
	protected $order = "ASC";
	protected $manga_id_array = array();
	
	public function __construct($db, $order_by, $order, $limit, $offset, $search, $manga_id_array) {
		$this->db = $db;
		
		$search_string = "";
		$group_by = "";
		$left_join_alt_names = "";
		
		$limit = sanitise_id($limit);
		$offset = sanitise_id($offset);
		
		foreach ($search as $key => $value) {
			$value = htmlentities(mysql_escape_mimic($value), ENT_QUOTES);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				case "manga_genres":
					$terms = explode(",", $value);
					foreach ($terms as $term) 
						$search_string .= "EXISTS (SELECT genre_id FROM mangadex_manga_genres WHERE mangadex_manga_genres.genre_id = $term AND mangadex_manga_genres.manga_id = mangadex_mangas.manga_id) AND ";					
					
					$group_by = "GROUP BY mangadex_mangas.manga_id ";
					break;
						
				case "manga_name":
					$terms = explode(" ", $value);
					foreach ($terms as $term) 
						$search_string .= "(mangadex_mangas.$key LIKE '%$term%' OR mangadex_manga_alt_names.alt_name LIKE '%$term%') AND ";
					
					$left_join_alt_names = "LEFT JOIN mangadex_manga_alt_names ON mangadex_manga_alt_names.manga_id = mangadex_mangas.manga_id";
					$group_by = "GROUP BY mangadex_mangas.manga_id ";
					break;
					
				case "manga_alpha":
					if ($value == "~")
						$search_string .= "mangadex_mangas.manga_name REGEXP '^[0-9._\+\#]' AND ";
					else 
						$search_string .= "mangadex_mangas.manga_name LIKE '$value%' AND ";
					break;
					
				case "manga_author":
					$search_string .= "mangadex_mangas.$key LIKE '%$value%' AND ";
					break;
				
				case "manga_artist":
					$search_string .= "mangadex_mangas.$key LIKE '%$value%' AND ";
					break;
					
				default:
					$search_string .= "mangadex_mangas.$key = $value AND ";
					break;

					
			}
		}
		
		if ($manga_id_array) {
			$manga_id_string = implode(",", $manga_id_array);
			$search_string .= "mangadex_mangas.manga_id IN ($manga_id_string) AND ";
		}
		
		$results = $this->db->get_results(" SELECT mangadex_mangas.manga_id, manga_image, manga_name, manga_author, manga_hentai, manga_rating, manga_views, manga_follows, manga_last_updated, manga_comments, lang_name, lang_flag 
			FROM mangadex_mangas 
			$left_join_alt_names 
			LEFT JOIN mangadex_languages ON mangadex_languages.lang_id = mangadex_mangas.manga_lang_id 
			WHERE $search_string 1=1 
			$group_by
			ORDER BY $order_by $order 
			LIMIT $limit OFFSET $offset"); 
		
		if ($results) {
			foreach ($results as $i => $manga) {
				$this->{$i} = new \stdClass();
				foreach ($manga as $key => $value) {
					$this->{$i}->$key = $value;
				}
				
				$slug = trim(preg_replace('/\W+/', '-', strtolower($this->{$i}->manga_name)), "-");
				$this->{$i}->manga_link = "<a href='/manga/{$this->$i->manga_id}/$slug'>{$this->$i->manga_name}</a>" . display_labels($this->{$i}->manga_hentai);
				
				$this->{$i}->manga_comments = ($this->{$i}->manga_comments) ? "<span class='label label-default' title='{$this->$i->manga_comments} comments'>" . display_glyphicon("comments", "far") . " {$this->$i->manga_comments}</span>" : "";
								
				if ($this->{$i}->manga_image) {
					$filename = get_ext($this->{$i}->manga_image, 0);
					$this->{$i}->logo = "<img src='/images/manga/{$this->$i->manga_id}.thumb.jpg' alt='image' />";
				}
				else 
					$this->{$i}->logo = "<img src='/images/manga/default.thumb.jpg' width='100%' alt='No manga image' />";
			}
		}
	}

	public function num_rows($db, $search, $manga_id_array) {
		$this->db = $db;
		
		$search_string = "";
		$group_by = "";
		$left_join_alt_names = "";
		
		foreach ($search as $key => $value) {
			$value = htmlentities(mysql_escape_mimic($value), ENT_QUOTES);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				case "manga_genres":
					$terms = explode(",", $value);
					foreach ($terms as $term) 
						$search_string .= "EXISTS (SELECT genre_id FROM mangadex_manga_genres WHERE mangadex_manga_genres.genre_id = $term AND mangadex_manga_genres.manga_id = mangadex_mangas.manga_id) AND ";					
					
					$group_by = "GROUP BY mangadex_mangas.manga_id ";
					break;
						
				case "manga_name":
					$terms = explode(" ", $value);
					foreach ($terms as $term) 
						$search_string .= "(mangadex_mangas.$key LIKE '%$term%' OR mangadex_manga_alt_names.alt_name LIKE '%$term%') AND ";
					
					$left_join_alt_names = "LEFT JOIN mangadex_manga_alt_names ON mangadex_manga_alt_names.manga_id = mangadex_mangas.manga_id";
					$group_by = "GROUP BY mangadex_mangas.manga_id ";
					break;
					
				case "manga_alpha":
					if ($value == "~")
						$search_string .= "mangadex_mangas.manga_name REGEXP '^[0-9._\+\#]' AND ";
					else 
						$search_string .= "mangadex_mangas.manga_name LIKE '$value%' AND ";
					break;
					
				case "manga_author":
					$search_string .= "mangadex_mangas.$key LIKE '%$value%' AND ";
					break;
				
				case "manga_artist":
					$search_string .= "mangadex_mangas.$key LIKE '%$value%' AND ";
					break;
					
				default:
					$search_string .= "mangadex_mangas.$key = $value AND ";
					break;

					
			}
		}
		
		if ($manga_id_array) {
			$manga_id_string = implode(",", $manga_id_array);
			$search_string .= "mangadex_mangas.manga_id IN ($manga_id_string) AND ";
		}
		
		return $this->db->get_var(" SELECT count(*) 
			FROM mangadex_mangas 
			$left_join_alt_names 
			LEFT JOIN mangadex_languages ON mangadex_languages.lang_id = mangadex_mangas.manga_lang_id 
			WHERE $search_string 1=1 
			$group_by "); 
		
	}
	
	
}

class Manga { 
	protected $db = null;
	protected $id = 0;
	
	public function __construct($db, $id) {
		$this->db = $db;
		$row = $this->db->get_row(" 
			SELECT * 
			FROM mangadex_mangas 
			LEFT JOIN mangadex_languages ON mangadex_languages.lang_id = mangadex_mangas.manga_lang_id 
			WHERE mangadex_mangas.manga_id = $id 
		"); 
		
		//does group exist
		$this->exists = ($this->db->num_rows == 0) ? FALSE : TRUE;
		
		//copy $row into $this
		if ($row) {  
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
			
			$this->manga_slug = trim(preg_replace('/\W+/', '-', strtolower($this->manga_name)), "-");
			
			$this->manga_comments = ($this->manga_comments) ? "<span class='badge'>$this->manga_comments</span>" : "";
			
			$this->logo = (!$this->manga_image) 
				? "<img src='/images/manga/default.png' width='100%' title='Default Manga image' alt='Default Manga image' />"
				: "<img src='/images/manga/{$this->manga_id}.{$this->manga_image}' width='100%' title='Manga image' alt='Manga image' />";
			
		}
	}
	
	public function get_total_chapters($db, $multi_lang) {
		if ($multi_lang) 
			return $this->db->get_var(" SELECT count(*) FROM mangadex_chapters WHERE manga_id = $this->manga_id AND chapter_deleted = 0 AND lang_id IN ($multi_lang) "); 
		else 
			return $this->db->get_var(" SELECT count(*) FROM mangadex_chapters WHERE manga_id = $this->manga_id AND chapter_deleted = 0 "); 
	}
	
	public function get_missing_chapters($db, $lang_id) {
		$this->db = $db;
		$results = $db->get_results(" SELECT chapter FROM mangadex_chapters WHERE manga_id = $this->manga_id AND lang_id = $lang_id and chapter_deleted = 0 ORDER BY abs(chapter) DESC ; ");
			
		foreach ($results as $row) {
			$chapter_array[] = $row->chapter;
		}
		
		$diff_array = array_diff(range(1, $chapter_array[0]), $chapter_array);
		
		$string = "";
		foreach($diff_array as $value) {
			$string .= $value . " ";
		}
		
		return $string;
	}

	public function get_manga_genres($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT genre_id FROM mangadex_manga_genres WHERE manga_id = $this->manga_id "); 
		
		$array = array();
		
		if ($results) {
			foreach ($results as $i => $row) {
				$array[] = $row->genre_id;
			}		
		}
		return $array; //array of genres
	}
	
	public function get_manga_alt_names($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT alt_name, lang_id FROM mangadex_manga_alt_names WHERE manga_id = $this->manga_id "); 
		
		$array["alt_name"] = array();
		$array["lang_id"] = array();
		if ($results) {
			foreach ($results as $i => $row) {
				$array["alt_name"][] = $row->alt_name;
				$array["lang_id"][] = $row->lang_id;
			}		
		}
		return $array; //array of genres
	}
	
	public function get_follows_user_id($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT user_id FROM mangadex_follow_user_manga WHERE manga_id = $this->manga_id "); 
		
		$array = array();
		if ($results) {
			foreach ($results as $i => $row) {
				$array[] = $row->user_id;
			}	
		}		
		return $array; //array of members
	}
}

class Chapter { 
	protected $db = null;
	protected $id = 0;
	
	public function __construct($db, $id) {
		$this->db = $db;
		$row = $this->db->get_row(" 
			SELECT mangadex_chapters.*, mangadex_mangas.manga_name, mangadex_groups.group_name, mangadex_languages.* 
			FROM mangadex_chapters 
			LEFT JOIN mangadex_mangas ON mangadex_mangas.manga_id = mangadex_chapters.manga_id 
			LEFT JOIN mangadex_groups ON mangadex_groups.group_id = mangadex_chapters.group_id 
			LEFT JOIN mangadex_languages ON mangadex_languages.lang_id = mangadex_chapters.lang_id 
			WHERE mangadex_chapters.chapter_id = $id 
		"); //session_key or user_id
		
		//does group exist
		$this->exists = ($this->db->num_rows == 0) ? FALSE : TRUE;
		
		//copy $row into $this
		if ($row) {  
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
		}
	}
	
	public function get_other_chapters($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT * FROM mangadex_chapters WHERE chapter_deleted = 0 AND lang_id = $this->lang_id AND manga_id = $this->manga_id GROUP BY chapter 
		order by (CASE volume WHEN '' THEN 1 END) asc, abs(volume) asc, abs(chapter) asc "); 
		
		foreach ($results as $i => $row) {
			if ($row->volume || $row->chapter) {
				$array["name"][$row->chapter_id] = (($row->volume) ? "Volume $row->volume " : "") . (($row->chapter) ? "Chapter $row->chapter " : ""); 
			}
			else {
				$array["name"][$row->chapter_id] = $row->title;
			}
			
			$array["id"][] = $row->chapter_id;
		}	
		
		return $array; //array of groups or group_ids
	}
	
	public function get_other_groups($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT mangadex_chapters.group_id, mangadex_chapters.chapter_id, mangadex_groups.group_name 
			FROM mangadex_chapters 
			LEFT JOIN mangadex_groups ON mangadex_groups.group_id = mangadex_chapters.group_id 
			WHERE mangadex_chapters.manga_id = $this->manga_id 
				AND mangadex_chapters.volume = '$this->volume' 
				AND mangadex_chapters.chapter = '$this->chapter' 
				AND mangadex_chapters.chapter_deleted = 0 
			ORDER BY mangadex_chapters.group_id ASC "); 
		
		foreach ($results as $i => $row) {
			$array["id"][$row->group_id] = $row->group_name;
			$array["chapter_id"][$row->group_id] = $row->chapter_id;
		}	
		
		return $array; //array of groups or group_ids
	}
	
	public function get_pages_of_prev_chapter($db, $id) {
		$this->db = $db;
		$page_order = $this->db->get_var(" SELECT page_order FROM mangadex_chapters WHERE chapter_id = $id "); 
		return count(explode(",", $page_order));
	}
}

class Users {
	protected $db = null;
	
	public function __construct($db, $order, $limit, $offset, $search) {
		$this->db = $db;
		
		$search_string = "";
		
		foreach ($search as $key => $value) {
			$value = mysql_escape_mimic($value);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				default:
					$search_string .= "mangadex_users.$key LIKE %$value% AND ";
					break;

					
			}
		}
		
		$results = $this->db->get_results(" SELECT * 
			FROM mangadex_users 
			LEFT JOIN mangadex_languages ON mangadex_users.language = mangadex_languages.lang_id 
			LEFT JOIN mangadex_user_levels ON mangadex_users.level_id = mangadex_user_levels.level_id 
			WHERE $search_string 1=1 
			ORDER BY $order 
			LIMIT $limit OFFSET $offset"); 
		
		foreach ($results as $i => $user) {
			$this->{$i} = new \stdClass();
			foreach ($user as $key => $value) {
				$this->{$i}->$key = $value;
			}
			
			$this->{$i}->user_slug = strtolower($this->{$i}->username);
			
			$this->{$i}->user_link = "<a style='color: #{$this->$i->level_colour}; ' id='{$this->$i->user_id}' href='/user/{$this->$i->user_id}/{$this->$i->user_slug}'>{$this->$i->username}</a>";
			
		}
	}
	
	public function num_rows($db, $search) {
		$this->db = $db;
		
		$search_string = "";
		
		foreach ($search as $key => $value) {
			$value = mysql_escape_mimic($value);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				default:
					$search_string .= "mangadex_groups.$key LIKE %$value% AND ";
					break;

					
			}
		}
		
		return $this->db->get_var(" SELECT count(*) FROM mangadex_users WHERE $search_string 1=1 "); 
	}
}

class User { //input token or user_id
	protected $db = null;
	protected $id = 0;
	protected $type = ""; //token or user_id
	//protected $timestamp;
	
	public function __construct($db, $id, $type) {
		$this->db = $db;
		$type = mysql_escape_mimic($type);
		$id = mysql_escape_mimic($id);
		
		$row = $this->db->get_row(" SELECT * 
			FROM mangadex_users 
			LEFT JOIN mangadex_user_levels ON mangadex_users.level_id = mangadex_user_levels.level_id 
			WHERE $type = '$id' 
			LIMIT 1; "); //session_key or user_id
		
		//does user exist
		$this->exists = ($this->db->num_rows == 0) ? FALSE : TRUE;
		
		if (!$this->exists)
			$row = $this->db->get_row(" SELECT * FROM mangadex_users, mangadex_user_levels WHERE mangadex_users.level_id = mangadex_user_levels.level_id AND user_id = 0 LIMIT 1; "); //session_key or user_id
		
		//copy $row into $this
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
			$this->user_slug = strtolower($this->username);
			
			$this->user_link = "<a style='color: #{$this->level_colour}; ' class='uploader' id='{$this->user_id}' href='/user/{$this->user_id}'>{$this->username}</a>";
			
			$this->logo = (!$this->avatar) ? "default2.png?v=4" : "$this->user_id.$this->avatar";
		}
	}
	
	public function get_total_chapters_uploaded($db) {
		$this->db = $db;
		return $this->db->get_var(" SELECT count(*) FROM mangadex_chapters WHERE user_id = $this->user_id "); 
	}
	
	public function get_unread_threads($db) {
		$this->db = $db;
		$total = $this->db->get_var(" SELECT count(*) FROM mangadex_pm_threads WHERE (sender_id = $this->user_id AND sender_read = 0) OR (recipient_id = $this->user_id AND recipient_read = 0) "); 
		if ($total) return "<span class='badge'>$total</span>";
	}
		
	public function get_groups($db, $keys = 0) {
		$this->db = $db;
		
		$results = $this->db->get_results(" SELECT mangadex_link_user_group.group_id, mangadex_groups.group_name 
			FROM mangadex_groups 
			LEFT JOIN mangadex_link_user_group ON mangadex_groups.group_id = mangadex_link_user_group.group_id 
			WHERE mangadex_link_user_group.user_id = $this->user_id "); 
		
		$array = array();
		if ($results) {
			foreach ($results as $i => $row) {
				if (!$keys) $array[$row->group_id] = $row->group_name; 
				else $array[] = $row->group_id;
			}	
		}
		return $array; //array of groups or group_ids
	}	
	
	public function get_followed_manga_ids($db) {
		$this->db = $db;
		
		$results = $this->db->get_results(" SELECT manga_id FROM mangadex_follow_user_manga WHERE user_id = $this->user_id; "); 
		
		$array = array();
		if ($results) {
			foreach ($results as $i => $row) {
				$array[] = $row->manga_id;
			}	
		}
		return $array; //array of manga_id
	}
	
	public function get_read_chapters($db) {
		$this->db = $db;
		
		$results = $this->db->get_results(" SELECT page_id, timestamp FROM mangadex_views WHERE user_id = $this->user_id AND type_id = 2; "); 
		
		$array["chapter_id"] = array();
		$array["timestamp"] = array();
		if ($results) {
			foreach ($results as $i => $row) {
				$array["chapter_id"][] = $row->page_id;
				$array["timestamp"][] = $row->timestamp;
			}	
		}
		return $array; //array of page_id
	}	
}

class Chapters {
	protected $db = null;
	
	protected $limit = 50;
	protected $offset = 0;
	protected $search = array();
	protected $manga_ids = array();
	protected $order = "upload_timestamp desc";
	
	public function __construct($db, $order, $limit, $offset, $search, $manga_id_array) {
		$this->db = $db;
		
		$search_string = "";
		
		foreach ($search as $key => $value) {
			$value = mysql_escape_mimic($value);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				case "multi_lang_id":
					$search_string .= "mangadex_chapters.lang_id IN ($value) AND ";
					break;
					
				case "manga_hentai":
					$search_string .= "mangadex_mangas.$key = $value AND ";
					break;
					
				case "upload_timestamp":
					$search_string .= "$key > $value AND ";
					break;
					
				case "grouped_chapters":
					$search_string .= "upload_timestamp < $value AND ";
					break;	
					
				default:
					$search_string .= "mangadex_chapters.$key = $value AND ";
					break;

					
			}
		}
		
		if (!empty($manga_id_array)) {
			$manga_id_string = implode(",", $manga_id_array);
			$search_string .= "mangadex_chapters.manga_id IN ($manga_id_string) AND ";
		}
		
		$results = $this->db->get_results(" SELECT mangadex_chapters.*, mangadex_languages.*, mangadex_users.username, mangadex_mangas.manga_name, mangadex_mangas.manga_image, mangadex_mangas.manga_hentai, mangadex_groups.group_name, mangadex_user_levels.level_colour  
			FROM mangadex_chapters 
			LEFT JOIN mangadex_mangas ON mangadex_mangas.manga_id = mangadex_chapters.manga_id 
			LEFT JOIN mangadex_groups ON mangadex_groups.group_id = mangadex_chapters.group_id 
			LEFT JOIN mangadex_languages ON mangadex_languages.lang_id = mangadex_chapters.lang_id 
			LEFT JOIN mangadex_users ON mangadex_chapters.user_id = mangadex_users.user_id 
			LEFT JOIN mangadex_user_levels on mangadex_users.level_id = mangadex_user_levels.level_id
			WHERE $search_string 1=1 
			ORDER BY $order 
			LIMIT $limit OFFSET $offset"); 
		
		if ($results) {
			foreach ($results as $i => $chapter) {
				$this->{$i} = new \stdClass();
				foreach ($chapter as $key => $value) {
					$this->{$i}->$key = $value;
				}
				
				$slug = trim(preg_replace('/\W+/', '-', strtolower($this->{$i}->manga_name)), "-");
				$this->{$i}->manga_link = "/manga/{$this->$i->manga_id}/$slug";
				
				if ($this->{$i}->manga_image) {
					$filename = get_ext($this->{$i}->manga_image, 0);
					$this->{$i}->logo = "<img src='/images/manga/{$this->$i->manga_id}.thumb.jpg' alt='image' />";
				}
				else 
					$this->{$i}->logo = "<img src='/images/manga/default.thumb.jpg' width='100%' alt='No manga image' />";
			}
		}
	}
	
	public function num_rows($db, $search, $manga_id_array) {
		$this->db = $db;
		
		$search_string = "";
		
		foreach ($search as $key => $value) {
			$value = mysql_escape_mimic($value);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				case "multi_lang_id":
					$search_string .= "mangadex_chapters.lang_id IN ($value) AND ";
					break;
					
				case "manga_hentai":
					$search_string .= "mangadex_mangas.$key = $value AND ";
					break;
					
				case "upload_timestamp":
					$search_string .= "$key > $value AND ";
					break;
					
				case "grouped_chapters":
					$search_string .= "upload_timestamp < $value AND ";
					break;	
					
				default:
					$search_string .= "mangadex_chapters.$key = $value AND ";
					break;

					
			}
		}
		
		if (!empty($manga_id_array)) {
			$manga_id_string = implode(",", $manga_id_array);
			$search_string .= "mangadex_chapters.manga_id IN ($manga_id_string) AND ";
		}
		
		return $this->db->get_var(" SELECT count(*) FROM mangadex_chapters LEFT JOIN mangadex_mangas ON mangadex_mangas.manga_id = mangadex_chapters.manga_id WHERE $search_string 1=1"); 
		
	}
}

class Languages {
	protected $db = null;
	protected $order_by = "lang_id";
	protected $order = "ASC";
	
	public function __construct($db, $order_by, $order) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT * FROM mangadex_languages ORDER BY $order_by $order "); 
		
		foreach ($results as $i => $torrent) {
			$this->{$i} = new \stdClass();
			foreach ($torrent as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}	
}

class Comments {
	protected $db = null;
	
	public function __construct($db, $id, $type = 1, $del = 0) { //1 = manga, 2 = group
		$this->db = $db;
		
		if ($type == 1) 
			$results = $this->db->get_results(" SELECT * FROM mangadex_comments_manga WHERE manga_id = $id AND comment_del = $del ORDER BY comment_timestamp DESC "); 
		else 
			$results = $this->db->get_results(" SELECT * FROM mangadex_comments_groups WHERE group_id = $id AND comment_del = $del ORDER BY comment_timestamp DESC "); 
		
		if ($results) {
			foreach ($results as $i => $comment) {
				$this->{$i} = new \stdClass();
				foreach ($comment as $key => $value) {
					$this->{$i}->$key = $value;
				}
			}
		}
	}	
	
	public function get_total_comments($id, $type = 1, $del = 0) { //1 = manga, 2 = group
		if ($type == 1) 
			$total = $this->db->get_var(" SELECT count(*) FROM mangadex_comments_manga WHERE manga_id = $id AND comment_del = $del "); 
		else 
			$total = $this->db->get_var(" SELECT count(*) FROM mangadex_comments_groups WHERE group_id = $id AND comment_del = $del "); 
		
		if ($total) return "<span class='badge'>$total</span>";
	}
}

class Comment { //input token or user_id
	protected $db = null;
	
	public function __construct($db, $id, $type = 1) { //1 = manga, 2 = group
		$this->db = $db;
		
		if ($type == 1) 
			$row = $this->db->get_row(" SELECT * FROM mangadex_comments_manga WHERE comment_id = $id "); 
		else 
			$row = $this->db->get_row(" SELECT * FROM mangadex_comments_groups WHERE comment_id = $id "); 
		
		//does comment exist
		$this->exists = ($this->db->num_rows == 0) ? FALSE : TRUE;
		
		//copy $row into $this
		foreach ($row as $key => $value) {
			$this->$key = $value;
		}
	}
}

class Groups {
	protected $db = null;
	
	public function __construct($db, $order, $limit, $offset, $search) {
		$this->db = $db;
		
		$search_string = "";
		
		foreach ($search as $key => $value) {
			$value = mysql_escape_mimic($value);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				case "group_name":
					$terms = explode(" ", $value);
					foreach ($terms as $term) 
						$search_string .= "mangadex_groups.$key LIKE '%$term%' AND ";
					break;
					
				default:
					$search_string .= "mangadex_groups.$key = $value AND ";
					break;

					
			}
		}
		
		$results = $this->db->get_results(" SELECT mangadex_groups.*, mangadex_languages.* 
			FROM mangadex_groups 
			LEFT JOIN mangadex_languages ON mangadex_groups.group_lang_id = mangadex_languages.lang_id 
			WHERE $search_string 1=1 
			ORDER BY $order 
			LIMIT $limit OFFSET $offset"); 
		
		foreach ($results as $i => $group) {
			$this->{$i} = new \stdClass();
			foreach ($group as $key => $value) {
				$this->{$i}->$key = $value;
			}
			
			$this->{$i}->group_slug = trim(preg_replace('/\W+/', '-', strtolower($this->$i->group_name)), "-");
			
			$this->{$i}->likes = ($this->$i->group_likes) ? "<span class='label label-success'>+{$this->$i->group_likes}</span>" : "";
			
			$this->{$i}->group_link = "<a class='group' id='{$this->$i->group_id}' data-src='{$this->$i->group_name}' href='/group/{$this->$i->group_id}/{$this->$i->group_slug}'>{$this->$i->group_name}</a>";
			
			$this->{$i}->irc_link = "irc://{$this->$i->group_irc_server}/{$this->$i->group_irc_channel}";
			
			$this->{$i}->group_comments = ($this->{$i}->group_comments) ? "<span class='label label-default' title='{$this->$i->group_comments} comments'>" . display_glyphicon("comments", "far") . " {$this->$i->group_comments}</span>" : "";
			
		}
	}
	
	public function num_rows($db, $search) {
		$this->db = $db;
		
		$search_string = "";
		
		foreach ($search as $key => $value) {
			$value = mysql_escape_mimic($value);
			$key = mysql_escape_mimic($key);
			switch ($key) {
				case "group_name":
					$terms = explode(" ", $value);
					foreach ($terms as $term) 
						$search_string .= "mangadex_groups.$key LIKE '%$term%' AND ";
					break;
					
				default:
					$search_string .= "mangadex_groups.$key = $value AND ";
					break;
			}
		}
		
		return $this->db->get_var(" SELECT count(*) FROM mangadex_groups WHERE $search_string 1=1 "); 
	}
}

class Group { 
	protected $db = null;
	protected $id = 0;
	
	public function __construct($db, $id) {
		$this->db = $db;
		$row = $this->db->get_row(" 
			SELECT mangadex_groups.*, mangadex_languages.*, mangadex_users.username, mangadex_users.user_id, mangadex_user_levels.level_colour 
			FROM mangadex_groups  
			LEFT JOIN mangadex_languages ON mangadex_groups.group_lang_id = mangadex_languages.lang_id 
			LEFT JOIN mangadex_users ON mangadex_groups.group_leader_id = mangadex_users.user_id 
			LEFT JOIN mangadex_user_levels ON mangadex_users.level_id = mangadex_user_levels.level_id 
			WHERE mangadex_groups.group_id = $id 
		"); //session_key or user_id
		
		//does group exist
		$this->exists = ($this->db->num_rows == 0) ? FALSE : TRUE;
		
		//copy $row into $this
		if ($row) {
			foreach ($row as $key => $value) {
				$this->$key = $value;
			}
			
			$this->group_slug = trim(preg_replace('/\W+/', '-', strtolower($this->group_name)), "-");
			
			$this->irc_link = "irc://{$this->group_irc_server}/{$this->group_irc_channel}";
			
			$this->likes = ($this->group_likes) ? "<span class='label label-success'>+{$this->group_likes}</span>" : "";
			
			$this->group_comments = ($this->group_comments) ? "<span class='badge'>$this->group_comments</span>" : "";
			
			$this->user_link = "<a style='color: #{$this->level_colour}; ' class='uploader' id='{$this->user_id}' href='/user/{$this->user_id}'>{$this->username}</a>";
		}
	}

	public function get_members($db) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_link_user_group.user_id, mangadex_users.username 
			FROM mangadex_link_user_group 
			LEFT JOIN mangadex_users ON mangadex_users.user_id = mangadex_link_user_group.user_id 
			WHERE mangadex_link_user_group.group_id = $this->group_id 
				AND mangadex_link_user_group.role = 2 
		"); //group_id
		
		$array = array();
		if ($results) {
			foreach ($results as $i => $row) {
				$array[$row->user_id] = $row->username; 
			}	
			natcasesort($array);
		}
		return $array; //array of members
	}
	
	public function get_manga_ids($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT manga_id FROM mangadex_chapters WHERE group_id = $this->group_id GROUP BY manga_id ORDER BY manga_id  "); //group_id
		
		$array = array();
		if ($results) {
			foreach ($results as $i => $row) {
				$array[] = $row->manga_id; 
			}	
		}
		return $array; //array of manga_id
	}	
	
	public function get_likes_user_id_ip_list($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT user_id, ip FROM mangadex_group_likes WHERE group_id = $this->group_id "); //group_id
		
		$array["user_id"] = array();
		$array["ip"] = array();
		
		if ($results) {
			foreach ($results as $i => $row) {
				if ($row->user_id > 1) $array["user_id"][] = $row->user_id;
				if ($row->ip) $array["ip"][] = $row->ip;
			}		
		}
		return $array; //array of members
	}
	

	
}

class User_levels {
	protected $db = null;
	
	public function __construct($db) {
		$this->db = $db;
		$results = $this->db->get_results(" SELECT * FROM mangadex_user_levels "); 
		
		foreach ($results as $i => $level) {
			$this->{$i} = new \stdClass();
			foreach ($level as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}	
}

class Visit_logs {
	protected $db = null;
	
	public function __construct($db, $table = "visits", $limit = 100) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_logs_$table.*, mangadex_users.username 
			FROM mangadex_logs_$table, mangadex_users 
			WHERE mangadex_logs_$table.visit_user_id = mangadex_users.user_id 
			ORDER BY visit_timestamp DESC 
			LIMIT $limit 
		"); 
		
		foreach ($results as $i => $row) {
			$this->{$i} = new \stdClass();
			foreach ($row as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}	
}

class Action_logs {
	protected $db = null;
	
	public function __construct($db, $limit = 100) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_logs_actions.*, mangadex_users.username 
			FROM mangadex_logs_actions, mangadex_users 
			WHERE mangadex_logs_actions.action_user_id = mangadex_users.user_id 
			ORDER BY action_timestamp DESC 
			LIMIT $limit 
		"); 
		
		foreach ($results as $i => $row) {
			$this->{$i} = new \stdClass();
			foreach ($row as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}	
}

class Chapter_reports {
	protected $db = null;
	
	public function __construct($db, $age, $limit = 300) {
		$this->db = $db;
		$age_operator = ($age == "new") ? "=" : ">";
		$results = $this->db->get_results(" 
			SELECT reports.*, mangadex_chapters.manga_id, reporter.username AS reported_name, actioned.username AS actioned_name
			FROM mangadex_chapter_reports AS reports
				LEFT JOIN mangadex_users AS reporter ON reports.report_user_id = reporter.user_id
				LEFT JOIN mangadex_users AS actioned ON reports.report_mod_user_id = actioned.user_id
				LEFT JOIN mangadex_chapters ON reports.report_chapter_id = mangadex_chapters.chapter_id 
			WHERE reports.report_mod_user_id $age_operator 0
			ORDER BY reports.report_timestamp DESC
			LIMIT $limit 
		"); 
		
		foreach ($results as $i => $report) {
			$this->{$i} = new \stdClass();
			foreach ($report as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}	
}

class PM_Threads { 
	protected $db = null;
	protected $user_id = 1;
	
	public function __construct($db, $user_id) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_pm_threads.*, mangadex_users.username 
			FROM mangadex_pm_threads 
			LEFT JOIN mangadex_users ON mangadex_pm_threads.sender_id = mangadex_users.user_id 
			WHERE mangadex_pm_threads.sender_id = $user_id OR mangadex_pm_threads.recipient_id = $user_id
			ORDER BY mangadex_pm_threads.thread_timestamp DESC 
			LIMIT 20 
		"); 
		
		//does user have messages
		$this->have_messages = ($this->db->num_rows == 0) ? FALSE : TRUE;
		
		if ($results) {
			foreach ($results as $i => $group) {
				$this->{$i} = new \stdClass();
				foreach ($group as $key => $value) {
					$this->{$i}->$key = $value;
				}
			}
		}
	}
	
	public function num_rows($db, $user_id) {
		$this->db = $db;	
		
		return $this->db->get_var(" SELECT count(*) FROM mangadex_pm_threads WHERE sender_id = $user_id OR recipient_id = $user_id ORDER BY thread_timestamp DESC LIMIT 20 "); 
	}
}

class PM_Thread { 
	protected $db = null;
	protected $id = 0;
	
	public function __construct($db, $id) {
		$this->db = $db;
		$row = $this->db->get_row(" SELECT * FROM mangadex_pm_threads WHERE thread_id = $id "); //session_key or user_id
		
		//copy $row into $this
		foreach ($row as $key => $value) {
			$this->$key = $value;
		}
	}
}


class PM_Msgs { 
	protected $db = null;
	protected $id = 1;
	
	public function __construct($db, $id) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_pm_msgs.*, mangadex_users.username 
			FROM mangadex_pm_msgs 
			LEFT JOIN mangadex_users ON mangadex_pm_msgs.user_id = mangadex_users.user_id 
			WHERE mangadex_pm_msgs.thread_id = $id 
			ORDER BY mangadex_pm_msgs.msg_timestamp ASC 
		"); 
		
		foreach ($results as $i => $group) {
			$this->{$i} = new \stdClass();
			foreach ($group as $key => $value) {
				$this->{$i}->$key = $value;
			}
		}
	}
}

class Forums { 
	protected $db = null;
	protected $parent_forum_id = 1;
	
	public function __construct($db, $parent_forum_id) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_forums.* 
			FROM mangadex_forums 
			WHERE mangadex_forums.forum_parent = $parent_forum_id 
			ORDER BY mangadex_forums.forum_name ASC 
		"); 
		
		if ($results) {
			foreach ($results as $i => $group) {
				$this->{$i} = new \stdClass();
				foreach ($group as $key => $value) {
					$this->{$i}->$key = $value;
				}
			}
		}
	}
	
	public function num_rows($db, $parent_forum_id) {
		$this->db = $db;	
		
		return $this->db->get_var(" SELECT count(*) FROM mangadex_forums WHERE mangadex_forums.forum_parent = $parent_forum_id "); 
	}
}

class Forum_Threads { 
	protected $db = null;
	protected $forum_id = 1;
	
	public function __construct($db, $forum_id, $limit, $offset) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_forum_threads.*, mangadex_users.username 
			FROM mangadex_forum_threads 
			LEFT JOIN mangadex_users ON mangadex_forum_threads.user_id = mangadex_users.user_id 
			WHERE mangadex_forum_threads.forum_id = $forum_id 
			ORDER BY mangadex_forum_threads.thread_timestamp DESC 
			LIMIT $limit OFFSET $offset
		"); 
		
		if ($results) {
			foreach ($results as $i => $group) {
				$this->{$i} = new \stdClass();
				foreach ($group as $key => $value) {
					$this->{$i}->$key = $value;
				}
			}
		}
	}
	
	public function num_rows($db, $forum_id) {
		$this->db = $db;	
		
		return $this->db->get_var(" SELECT count(*) FROM mangadex_forum_threads WHERE mangadex_forum_threads.forum_id = $forum_id "); 
	}
	
	public function get_breadcrumb($db, $forum_id) {
		$this->db = $db;	
		
		$string = "<ol class='breadcrumb'>
			<li><a href='/forums'>Home</a></li>";
			
		$forum = $this->db->get_row(" SELECT forum_id, forum_name, forum_parent FROM mangadex_forums WHERE forum_id = $forum_id LIMIT 1 "); 
		
		while($forum->forum_parent) {
			$forum = $this->db->get_row(" SELECT forum_id, forum_name, forum_parent FROM mangadex_forums WHERE forum_id = $forum->forum_parent LIMIT 1 "); 
			$forum_array[$forum->forum_id] = $forum->forum_name;
		}
		
		$forum_array_rev = array_reverse($forum_array, true);
		foreach ($forum_array_rev as $key => $value) {
			$string .= "<li><a href='/forum/$key'>$value</a></li>";
		}
		
		$forum_name = $this->db->get_var(" SELECT forum_name FROM mangadex_forums WHERE forum_id = $forum_id LIMIT 1 "); 
		
		$string .= "<li>$forum_name</li>
			</ol>";
			
		return $string;
	}	
}



class Forum_Posts { 
	protected $db = null;
	protected $id = 1;
	
	public function __construct($db, $thread_id, $limit, $offset) {
		$this->db = $db;
		$results = $this->db->get_results(" 
			SELECT mangadex_forum_posts.*, mangadex_users.username, mangadex_users.avatar, mangadex_user_levels.level_colour 
			FROM mangadex_forum_posts 
			LEFT JOIN mangadex_users ON mangadex_forum_posts.user_id = mangadex_users.user_id 
			LEFT JOIN mangadex_user_levels ON mangadex_users.user_id = mangadex_user_levels.level_id 
			WHERE mangadex_forum_posts.thread_id = $thread_id 
			ORDER BY mangadex_forum_posts.post_timestamp ASC 
			LIMIT $limit OFFSET $offset 
		"); 
		
		foreach ($results as $i => $group) {
			$this->{$i} = new \stdClass();
			foreach ($group as $key => $value) {
				$this->{$i}->$key = $value;
			}
			
			$this->{$i}->user_link = "<a style='color: #" . $this->{$i}->level_colour . "; ' class='uploader' id='" . $this->{$i}->user_id . "' href='/user/" . $this->{$i}->user_id . "'>" . $this->{$i}->username . "</a>";
			
			$this->{$i}->logo = (!$this->{$i}->avatar) ? "default2.png" : "{$this->$i->user_id}.{$this->$i->avatar}";
		}
	}
	
	public function num_rows($db, $thread_id) {
		$this->db = $db;	
		
		return $this->db->get_var(" SELECT count(*) FROM mangadex_forum_posts WHERE mangadex_forum_posts.post_id = $thread_id "); 
	}
	
	public function get_thread_name($db, $thread_id) {
		$this->db = $db;	
		
		return $this->db->get_var(" SELECT thread_name FROM mangadex_forum_threads WHERE mangadex_forum_threads.thread_id = $thread_id "); 
	}
		
	public function get_breadcrumb($db, $thread_id) {
		$this->db = $db;	
		
		$thread = $this->db->get_row(" SELECT forum_id, thread_name FROM mangadex_forum_threads WHERE mangadex_forum_threads.thread_id = $thread_id lIMIT 1 "); 
		
		$string = "<ol class='breadcrumb'>
			<li><a href='/forums'>Home</a></li>";
			
		$forum = $this->db->get_row(" SELECT forum_id, forum_name, forum_parent FROM mangadex_forums WHERE forum_id = $thread->forum_id LIMIT 1 "); 
		
		while($forum->forum_parent) {
			$forum = $this->db->get_row(" SELECT forum_id, forum_name, forum_parent FROM mangadex_forums WHERE forum_id = $forum->forum_parent LIMIT 1 "); 
			$forum_array[$forum->forum_id] = $forum->forum_name;
		}
		
		$forum_array_rev = array_reverse($forum_array, true);
		foreach ($forum_array_rev as $key => $value) {
			$string .= "<li><a href='/forum/$key'>$value</a></li>";
		}
		
		$forum = $this->db->get_row(" SELECT forum_id, forum_name FROM mangadex_forums WHERE forum_id = $thread->forum_id LIMIT 1 "); 
		
		$string .= "<li><a href='/forum/$forum->forum_id'>$forum->forum_name</a></li>
			<li class='active'>$thread->thread_name</li>
			</ol>";
			
		return $string;
	}	
}
?>