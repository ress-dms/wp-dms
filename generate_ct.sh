#!/bin/bash

# Define custom post types
post_types=("article" "event" "location")

# Number of posts to create per post type
num_posts=5

# Loop through each custom post type
for post_type in "${post_types[@]}"; do
    echo "Creating $num_posts posts for $post_type..."

    for ((i = 1; i <= num_posts; i++)); do
        # Fetch a random attachment ID from the database (assuming attachments are images)
        image_id=$(wp db query "SELECT ID FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY RAND() LIMIT 1;" --skip-column-names)

        if [ -z "$image_id" ]; then
            echo "No images found in the media library. Skipping image assignment."
            image_id=0
        fi

        # Create a new post with lorem ipsum content and a random featured image
        post_id=$(wp post create --post_type="$post_type" \
            --post_title="$post_type Title $i" \
            --post_content="$(curl -s 'https://loripsum.net/api/1/short')" \
            --post_status="publish" \
            --post_author=1 \
            --post_excerpt="$(curl -s 'https://loripsum.net/api/1/short')" \
            --porcelain)

        if [ "$image_id" != 0 ]; then
            # Attach a random image as the featured image
            wp post meta update $post_id _thumbnail_id $image_id
            echo "Created post ID $post_id with featured image ID $image_id"
        else
            echo "Created post ID $post_id without a featured image"
        fi
    done
done

# make 2 custom taxonomies with the optiion 'custom_taxonomies'
generate_random_taxonomy() {
    TAXONOMY_NAME="taxonomy_$(openssl rand -hex 3)"
    TAXONOMY_LABEL="Taxonomy Label $(openssl rand -hex 2)"
    echo "$TAXONOMY_NAME" "$TAXONOMY_LABEL"
}

# Loop to create two random taxonomies
for i in {1..2}; do
    # Generate random taxonomy name and label
    read TAXONOMY_NAME TAXONOMY_LABEL < <(generate_random_taxonomy)

    # Insert taxonomy into the database using wp-cli
    wp eval "
  \$custom_taxonomies = get_option('custom_taxonomies', array());
  \$custom_taxonomies[] = array(
    'name' => '$TAXONOMY_NAME',
    'label' => '$TAXONOMY_LABEL',
    // post_type is random but not 'post' or 'page'
    'post_types' => array('${post_types[$RANDOM % ${#post_types[@]}]}')
  );
  update_option('custom_taxonomies', \$custom_taxonomies);
  
  // Register the new taxonomy
  register_taxonomy('$TAXONOMY_NAME', array('post', 'page'), array(
    'labels' => array(
      'name' => __('$TAXONOMY_LABEL'),
      'singular_name' => __('$TAXONOMY_LABEL')
    ),
    'hierarchical' => true,
    'public' => true,
    'show_in_rest' => true
  ));
  
  echo 'Taxonomy $TAXONOMY_NAME created.';
  "
done
