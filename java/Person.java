import java.util.ArrayList;

public class Person {
    private Person prev;
    private int id;
    private String name;
    private int artist_id;
    private String artist;
    private int album_id;
    private String album;
    private ArrayList<Person> next;

    public Person( Person prev, int id, String name, int artist_id, String artist,
		   int album_id, String album ) {
		this.prev = prev;
		this.id = id;
		this.name = name;
		this.artist_id = artist_id;
		this.artist = artist;
		this.album_id = album_id;
        this.album = album;
		this.next = new ArrayList<Person>();
    }

    public int getId() {
    	return id;
    }

    public ArrayList<Person> getNext() {
    	return next;
    }

    public String getName() {
    	return name;
    }

    public void setNext( Person p ) {
    	next.add( p );
    }

    public int getDepth() {
    	int count = 0;
    	Person walk = this;
    	while( walk.prev != null ) {
    		walk = walk.prev;
    		++count;
    	}
    	return count;
    }

    public String toString() {
		int i, count;
		ArrayList<String> path = new ArrayList<>();
		Person node = this;
		StringBuffer returnMe = new StringBuffer();

		if( node.prev == null )
		    returnMe.append( name + " (root node)" );
		while( node.prev != null ) {
		    path.add( "<a href=\"person.php?person=" + node.prev.id + "\">"
				+ node.prev.name + "</a> played on "
				+ "<a href=\"album.php?album=\"" + node.album_id + "\"><i>" + node.album + "</i></a> "
				+ "by <a href=\"album_artist.php?artist=" + node.artist_id + "\">" + node.artist + "</a> "
				+ "with <a href=\"person.php?person=" + node.id + "\">" + node.name + "</a>." );
		    node = node.prev;
		}

		for( i = path.size() - 1, count = 1; i >= 0; --i, ++count )
		    returnMe.append( count + ": " + path.get( i ) + "<br />\n" );
		return returnMe.toString();
    }
}
		      