<?php

include_once 'Database.php';
include_once 'User.php';

class Activity
{
    public object $con;
    private object $user;
    private object $listing;

    public function __construct()
    {
        $this -> con = new Database;
        $this -> user = new User;
        $this -> listing = new Listing;
    }

    /**
     * @param array $post
     * @return int|false
     * Crea un post mediante $post['user_id'] y $post['content'] y lo asigna a un usuario.
     */
    public function post(array $post): int|false
    {
        if (isset($post['content']) && isset($post['user_id'])) {
            if ($this -> user -> validateSession()) {
                if ($this -> con -> db -> execute_query('INSERT INTO `post`(`post_id`, `user_id`, `content`, `date`) VALUES (null, ?, ?, default)', [$post['user_id'], $post['content']])) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param array $data
     * @return bool
     * Se crea un post cuando un usuario (que permita esto en sus ajustes) actualiza su lista de anime|manga.
     * Esto signfica que se creará un post personalizado para cada vez que el usuario: ->
     * <- añada una entrada a su lista, sume un episodio|capítulo a una entrada de su lista o cambie el valor de la columna `status` en las tablas de las listas.
     */
    public function listUpdate(array $data): bool
    {
        if (isset($data['user_id']) && isset($data['medium']) && isset($data['medium_id'])) {
            if ($this -> user -> validateSession()) {
                if ($this -> listing -> existsWithID($data['medium'], $data['medium_id'])) {
                    $data['medium'] === 'anime' ? $current = 'watched' : $current = 'read';
                    $data['medium'] === 'anime' ? $current2 = 'episode' : $current2 = 'chapter';
                    $current3 = $this -> user -> getEpisodesOrChapters($data['medium'], $data['medium_id'], $data['user_id']);
                    $current4 = $this -> listing -> getTitle($data['medium'], $data['medium_id']);
                    $post['content'] = 'I have ' . $current . " " . $current2 . " " .  $current3 . ' from ' . $current4 . '.';
                    $post['user_id'] = $data['user_id'];
                    if ($this -> post($post)) {
                        return true;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param array $data
     * @return bool
     * Se añade una fila a la entrada de post_like cuando un usuario da click sobre el icono de corazón de un post.
     */
    public function like(array $data): bool
    {
        if (isset($data['post_id']) && $data['user_id']) {
            if ($this -> user -> validateSession()) {
                 if ($this -> con -> db -> execute_query('INSERT INTO `post_like` VALUES(?, ?, default)', [$data['post_id'], $data['user_id']])) {
                    return true;
                 }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return array $select
     * Hace una lista con todos los anime y manga de la base de datos para mostrar en un select dentro del wrapper de creación de post.
     * Con esto, un usuario puede relacionar un post a un anime o manga.
     */
    public function getSelect(): array
    {
        $animes = $this -> con -> db -> execute_query('SELECT title FROM anime ORDER BY title');
        $mangas = $this -> con -> db -> execute_query('SELECT title FROM manga ORDER BY title');

        for ($i = 0; $i < $animes -> num_rows; $i++) {
            $anime = $animes -> fetch_column();
            $select[] = $anime . ' (anime)';
        }

        for ($i = 0; $i < $mangas -> num_rows; $i++) {
            $manga = $mangas -> fetch_column();
            $select[] = $manga . ' (manga)';
        }

        return $select;
    }

    public function setPostRelation(string $medium, int $post_id, int $user_id, int $entry): bool
    {
        if ($this -> con -> db -> execute_query('INSERT INTO post_'.$medium.' VALUES (?, ?, ?)', [$post_id, $user_id, $entry])) {
            return true;
        } else {
            return false;
        }
    }

    public function exists(int $post_id): bool
    {
        if ($this -> con -> db -> execute_query('SELECT * FROM post WHERE post_id = ?', [$post_id]) -> num_rows === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $post_id
     * @return array
     * Este método requiere el previo uso del método Activity -> exists() para que no de error en caso de no existir el post.
     */
    public function getPost(int $post_id): array
    {
        $result = $this -> con -> db -> execute_query('SELECT * FROM post WHERE post_id = ?', [$post_id]) -> fetch_assoc();
        
        foreach ($result as $key => $value) {
            if ($key === 'date') {
                // Recogida de las fechas para crear el tiempo que ha pasado desde la creación de cada post mediante mi función timeAgo().
                $post['post']['date'] = $value;
                $current = date('Y-m-d H:i:s');
                $reference = $post['post']['date'];
                $timeAgo = timeAgo($current, $reference);
                $post['post']['time_ago'] = $timeAgo;
            } else if ($key === 'user_id') {
                $post['user']['user_id'] = $value;
            } else {
                $post['post'][$key] = $value;
            }
        }
        
        $post['user']['username'] = $this -> user -> getUsername($post['user']['user_id']);

        $animeId = $this -> con -> db -> execute_query('SELECT anime_id FROM `post_anime` WHERE post_id = ?', [$post['post']['post_id']]);
        $mangaId = $this -> con -> db -> execute_query('SELECT manga_id FROM `post_manga` WHERE post_id = ?', [$post['post']['post_id']]);
        if ($animeId -> num_rows === 1) {
            $post['post']['medium'] = 'anime';
            $post['post']['medium_id'] = $animeId -> fetch_column();
            $post['post']['medium_title'] = $this -> con -> db -> execute_query('SELECT title FROM anime WHERE anime_id = ?', [$post['post']['medium_id']]) -> fetch_column();
        } else if ($mangaId -> num_rows === 1) {
            $post['post']['medium'] = 'manga';
            $post['post']['medium_id'] = $mangaId -> fetch_column();
            $post['post']['medium_title'] = $this -> con -> db -> execute_query('SELECT title FROM manga WHERE manga_id = ?', [$post['post']['medium_id']]) -> fetch_column();
        }

        if ($userInfo = $this -> user -> getInfoLess($post['user']['user_id'])) {
            foreach ($userInfo as $key => $value) {
                $post['user'][$key] = $value;
            }
        }
        
        return $post;
    }
}